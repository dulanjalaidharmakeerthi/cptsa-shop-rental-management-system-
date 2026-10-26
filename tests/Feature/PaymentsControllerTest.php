<?php

namespace Tests\Feature;

use App\Models\Payment;
use App\Models\Shop;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentsControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_payment_records_store_selected_months_for_history_display(): void
    {
        $user = User::factory()->create();

        $shop = Shop::create([
            'shop_number' => 'S-100',
            'shop_owner_name' => 'Test Owner',
            'shop_owner_nic' => '123456789V',
            'agreement_start' => '2024-01-01',
            'monthly_rent' => 1000,
            'rate' => 5,
            'due_payment' => 0,
            'due_interest' => 0,
        ]);

        $response = $this->actingAs($user)->postJson(route('payments.record'), [
            'shop_id' => $shop->id,
            'receipt_number' => 'RCPT-001',
            'amount' => 2000,
            'interest' => 10,
            'paid_at' => '2026-06-25',
            'selected_months' => json_encode([1, 2]),
            'selected_dues' => json_encode([2]),
            'year' => 2026,
        ]);

        $response->assertOk();

        $payments = Payment::where('shop_id', $shop->id)->get();
        $this->assertCount(2, $payments);
        $this->assertSame('Jan 2026, Feb 2026', $payments->first()->payable_months);
    }

    public function test_due_balance_calculates_from_march_2026_agreement_start(): void
    {
        $user = User::factory()->create();

        $shop = Shop::create([
            'shop_number' => 'S-200',
            'shop_owner_name' => 'March Start Owner',
            'shop_owner_nic' => '987654321V',
            'agreement_start' => '2026-03-01',
            'agreement_end' => null,
            'monthly_rent' => 1000,
            'rate' => 5,
            'due_payment' => 0,
            'due_interest' => 0,
            'record_year' => 2026,
        ]);

        $response = $this->actingAs($user)->getJson(route('payments.search', ['q' => 'March Start Owner']));
        $response->assertOk();

        $data = $response->json();
        $this->assertCount(1, $data);

        $shopResult = $data[0];
        $this->assertSame(1000.00, $shopResult['due_payment']);
        $this->assertSame(50.00, $shopResult['due_interest']);
        $this->assertSame(1050.00, $shopResult['total_need_to_pay']);
    }

    public function test_current_month_rent_becomes_due_after_the_28th_at_noon(): void
    {
        $user = User::factory()->create();
        $shop = Shop::create([
            'shop_number' => 'S-300',
            'shop_owner_name' => 'Current Month Owner',
            'shop_owner_nic' => '111222333V',
            'agreement_start' => '2026-10-01',
            'monthly_rent' => 1000,
            'rate' => 5,
            'record_year' => 2026,
        ]);

        try {
            Carbon::setTestNow('2026-10-28 12:00:00');
            $beforeDueTime = $this->actingAs($user)->getJson(route('payments.search', ['q' => 'Current Month Owner']));
            $beforeDueTime->assertOk();
            $this->assertEquals(0, (float) $beforeDueTime->json('0.due_payment'));
            $this->assertCount(0, $beforeDueTime->json('0.unpaid_months'));

            Carbon::setTestNow('2026-10-28 12:00:01');
            $afterDueTime = $this->getJson(route('payments.search', ['q' => 'Current Month Owner']));
            $afterDueTime->assertOk();
            $this->assertEquals(1000, (float) $afterDueTime->json('0.due_payment'));
            $this->assertEquals(50, (float) $afterDueTime->json('0.due_interest'));
            $this->assertCount(1, $afterDueTime->json('0.unpaid_months'));
        } finally {
            Carbon::setTestNow();
        }
    }

    public function test_current_month_interest_can_only_be_selected_after_the_28th_at_noon(): void
    {
        $user = User::factory()->create();
        $shop = Shop::create([
            'shop_number' => 'S-400',
            'shop_owner_name' => 'Interest Cutoff Owner',
            'shop_owner_nic' => '222333444V',
            'agreement_start' => '2026-10-01',
            'monthly_rent' => 1000,
            'rate' => 5,
            'record_year' => 2026,
        ]);

        try {
            Carbon::setTestNow('2026-10-28 12:00:00');
            $beforeCutoff = $this->actingAs($user)->postJson(route('payments.record'), [
                'shop_id' => $shop->id,
                'receipt_number' => 'RCPT-CUTOFF',
                'amount' => 1000,
                'interest' => 50,
                'paid_at' => '2026-10-28',
                'selected_months' => json_encode([10]),
                'selected_dues' => json_encode([10]),
                'year' => 2026,
            ]);
            $beforeCutoff->assertUnprocessable()
                ->assertJson(['message' => 'Interest is not due for the selected month yet']);

            $this->assertSame(0, Payment::where('shop_id', $shop->id)->count());
        } finally {
            Carbon::setTestNow();
        }
    }
}
