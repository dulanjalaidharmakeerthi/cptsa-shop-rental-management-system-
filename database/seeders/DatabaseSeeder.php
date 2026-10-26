<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create demo users for quick testing
        User::factory()->create([
            'name' => 'Manager User',
            'email' => 'manager@example.com',
            'phone' => '0700000001',
            'position' => 'Manager',
            'password' => Hash::make('password'),
        ]);

        User::factory()->create([
            'name' => 'Staff User',
            'email' => 'staff@example.com',
            'phone' => '0700000002',
            'position' => 'Staff',
            'password' => Hash::make('password'),
        ]);

        User::factory()->create([
            'name' => 'Cashier User',
            'email' => 'cashier@example.com',
            'phone' => '0700000003',
            'position' => 'Cashier',
            'password' => Hash::make('password'),
        ]);

        $this->call(ShopSeeder::class);
    }
}
