<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Admin;

class AdminSeeder extends Seeder
{
    public function run()
    {
        $username = 'k14058.piyumika@gmail.com';
        $password = 'dulanjalidarmakirthi@gmail.com';

        // Delete existing admin if exists
        Admin::where('username', $username)->delete();

        // Create new admin with correct hashed password
        Admin::create([
            'name' => 'Primary Admin',
            'username' => $username,
            'email' => $username,
            'password' => Hash::make($password),
        ]);
    }
}
