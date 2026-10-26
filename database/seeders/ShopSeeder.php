<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Shop;

class ShopSeeder extends Seeder
{
    public function run()
    {
        Shop::create([
            'shop_number' => 'RK-001',
            'shop_name' => 'Test Shop',
            'shop_owner_name' => 'Test Owner',
            'shop_owner_nic' => '123456789V',
            'monthly_rent' => 5000.00,
            'rate' => 5.00,
            'bus_stand_id' => 1, // assuming exists
            'record_year' => '2026',
        ]);
    }
}
