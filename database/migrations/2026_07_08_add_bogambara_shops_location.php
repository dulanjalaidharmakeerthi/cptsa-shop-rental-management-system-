<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Insert Bogambara Shops location
        DB::table('shops')->insert([
            'shop_name' => 'Bogambara Shops',
            'shop_number' => 'BOGAMBARA-001',
            'shop_owner_name' => 'Bogambara Owner',
            'shop_owner_nic' => 'N/A',
            'shop_owner_phone' => '+94-0-000-000',
            'location' => 'Bogambara Shops',
            'shop_address' => 'Bogambara Shops',
            'monthly_rent' => 0,
            'rate' => 0,
            'advance_payment' => 0,
            'opening_balance' => 0,
            'remarks' => 'Location: Bogambara Shops - Created for location mapping',
            'record_year' => date('Y'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Delete the Bogambara Shops record
        DB::table('shops')->where('shop_name', 'Bogambara Shops')->delete();
    }
};
