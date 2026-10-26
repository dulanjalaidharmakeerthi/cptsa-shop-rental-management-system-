<?php

namespace Tests\Feature;

use App\Models\Shop;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShopChargeHistoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_rate_and_charge_changes_are_saved_and_returned_with_the_shop(): void
    {
        $shop = Shop::create([
            'shop_number' => 'S-501',
            'shop_owner_name' => 'History Owner',
            'shop_value' => 120000,
            'monthly_rent' => 1000,
            'rate' => 5,
            'year_charge' => 12000,
            'record_year' => 2026,
        ]);

        $response = $this->withSession(['admin_id' => 1])->post(route('admin.shop_management.update'), [
            'shop_id' => $shop->id,
            'shop_number' => $shop->shop_number,
            'shop_owner_name' => $shop->shop_owner_name,
            'shop_value' => 120000,
            'monthly_rent' => 1200,
            'rate' => 6,
            'year_charge' => 14400,
            'due_payment' => 0,
            'due_interest' => 0,
            'year_interest' => 0,
            'year_total_taken' => 0,
            'deduct_payment' => 0,
            'deduct_interest' => 0,
            'record_year' => 2026,
        ]);

        $response->assertRedirect(route('admin.shop_management.index'));
        $this->assertDatabaseHas('shop_charge_histories', [
            'shop_id' => $shop->id,
        ]);

        $searchResponse = $this->getJson(route('admin.shop_management.search', ['owner' => 'History Owner']));
        $searchResponse->assertOk()
            ->assertJsonPath('rate', '6.00')
            ->assertJsonPath('charge_histories.0.old_values.rate', '5.00')
            ->assertJsonPath('charge_histories.0.old_values.monthly_interest', '50.00')
            ->assertJsonPath('charge_histories.0.new_values.rate', '6.00')
            ->assertJsonPath('charge_histories.0.new_values.monthly_interest', '72.00');
    }

    public function test_updating_other_shop_details_does_not_create_charge_history(): void
    {
        $shop = Shop::create([
            'shop_number' => 'S-502',
            'shop_owner_name' => 'Unchanged Charge Owner',
            'shop_value' => 120000,
            'monthly_rent' => 1000,
            'rate' => 5,
            'year_charge' => 12000,
            'record_year' => 2026,
        ]);

        $this->withSession(['admin_id' => 1])->post(route('admin.shop_management.update'), [
            'shop_id' => $shop->id,
            'shop_number' => $shop->shop_number,
            'shop_owner_name' => $shop->shop_owner_name,
            'shop_value' => 120000,
            'monthly_rent' => 1000,
            'rate' => 5,
            'year_charge' => 12000,
            'due_payment' => 25,
            'due_interest' => 0,
            'year_interest' => 0,
            'year_total_taken' => 0,
            'deduct_payment' => 0,
            'deduct_interest' => 0,
            'record_year' => 2026,
        ])->assertRedirect(route('admin.shop_management.index'));

        $this->assertDatabaseCount('shop_charge_histories', 0);
    }
}
