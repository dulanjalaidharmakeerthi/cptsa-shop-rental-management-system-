<?php

namespace Tests\Feature;

use App\Models\Payment;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardAnalyticsTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_shows_current_month_collection_and_outstanding_rent(): void
    {
        $shop = $this->createShop(1200);
        Payment::create([
            'shop_id' => $shop->id,
            'amount' => 500,
            'interest' => 0,
            'receipt_number' => 'RENT-500',
            'paid_at' => now(),
        ]);

        $response = $this->actingAs(User::factory()->create())
            ->get(route('managedashboard'));

        $response->assertOk()
            ->assertDontSee('Expired Agreements')
            ->assertDontSee('Pending this month')
            ->assertSee('Expected rent vs. collected')
            ->assertSee('Rs 1,200.00')
            ->assertSee('Rs 500.00')
            ->assertSee('Rs 700.00')
            ->assertSee('Collection in progress');
    }

    public function test_dashboard_highlights_collections_above_expected_rent(): void
    {
        $shop = $this->createShop(1000);
        Payment::create([
            'shop_id' => $shop->id,
            'amount' => 1250,
            'interest' => 0,
            'receipt_number' => 'RENT-1250',
            'paid_at' => now(),
        ]);

        $response = $this->actingAs(User::factory()->create())
            ->get(route('managedashboard'));

        $response->assertOk()
            ->assertSee('Above target')
            ->assertSee('Collected above expected')
            ->assertSee('Rs 250.00')
            ->assertSee('aria-valuenow="100"', false);
    }

    private function createShop(float $monthlyRent): Shop
    {
        return Shop::create([
            'shop_number' => 'AN-' . $monthlyRent,
            'shop_name' => 'Analytics Shop',
            'shop_owner_name' => 'Dashboard Owner',
            'monthly_rent' => $monthlyRent,
            'record_year' => now()->year,
        ]);
    }
}
