<?php

namespace Tests\Feature;

use App\Models\BusStand;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportDueFiltersTest extends TestCase
{
    use RefreshDatabase;

    public function test_due_report_filters_by_bus_stand_and_shows_shop_details(): void
    {
        $user = User::factory()->create();

        $standA = BusStand::create(['name' => 'Bus Stand A']);
        $standB = BusStand::create(['name' => 'Bus Stand B']);

        $shopA = Shop::create([
            'shop_number' => 'S-101',
            'shop_name' => 'Alpha Shop',
            'shop_owner_name' => 'Owner A',
            'shop_owner_nic' => '111111111V',
            'bus_stand_id' => $standA->id,
            'monthly_rent' => 1200,
            'rate' => 5,
            'due_payment' => 0,
            'due_interest' => 0,
            'record_year' => 2025,
            'agreement_start' => '2025-01-01',
        ]);

        Shop::create([
            'shop_number' => 'S-202',
            'shop_name' => 'Beta Shop',
            'shop_owner_name' => 'Owner B',
            'shop_owner_nic' => '222222222V',
            'bus_stand_id' => $standB->id,
            'monthly_rent' => 1500,
            'rate' => 6,
            'due_payment' => 0,
            'due_interest' => 0,
            'record_year' => 2025,
            'agreement_start' => '2025-01-01',
        ]);

        $response = $this->actingAs($user)->get(route('reports.due.index', ['bus_stand_id' => $standA->id]));

        $response->assertOk();
        $response->assertSee('Shop Number');
        $response->assertViewHas('dueData', function ($dueData) use ($shopA) {
            $this->assertCount(1, $dueData);
            $this->assertSame($shopA->id, $dueData[0]['shop']->id);
            return true;
        });
    }

    public function test_tenant_report_shows_due_analytics_for_all_matching_shops(): void
    {
        $user = User::factory()->create();
        $stand = BusStand::create(['name' => 'Analytics Stand']);

        Shop::create([
            'shop_number' => 'AN-101',
            'shop_name' => 'Analytics Alpha',
            'shop_owner_name' => 'Analytics Owner A',
            'bus_stand_id' => $stand->id,
            'monthly_rent' => 1000,
            'due_payment' => 3000,
            'due_interest' => 1000,
            'record_year' => now()->year,
        ]);

        Shop::create([
            'shop_number' => 'AN-102',
            'shop_name' => 'Analytics Beta',
            'shop_owner_name' => 'Analytics Owner B',
            'bus_stand_id' => $stand->id,
            'monthly_rent' => 1000,
            'due_payment' => 2000,
            'due_interest' => 0,
            'record_year' => now()->year,
        ]);

        Shop::create([
            'shop_number' => 'AN-103',
            'shop_name' => 'Other Stand Shop',
            'shop_owner_name' => 'Other Owner',
            'monthly_rent' => 1000,
            'due_payment' => 9000,
            'due_interest' => 1000,
            'record_year' => now()->year,
        ]);

        $response = $this->actingAs($user)
            ->get(route('reports.tenants.index', ['bus_stand_id' => $stand->id]));

        $response->assertOk()
            ->assertSee('Outstanding balance analytics')
            ->assertSee('Rs 5,000.00')
            ->assertSee('Rs 1,000.00')
            ->assertSee('Rs 6,000.00')
            ->assertViewHas('tenantSummary', function ($summary) {
                $this->assertSame(2, (int) $summary->shop_count);
                $this->assertSame(2, (int) $summary->shops_with_outstanding_balance);
                $this->assertEquals(83.3333, round($summary->due_payment_share, 4));
                $this->assertEquals(16.6667, round($summary->due_interest_share, 4));
                return true;
            });
    }
}
