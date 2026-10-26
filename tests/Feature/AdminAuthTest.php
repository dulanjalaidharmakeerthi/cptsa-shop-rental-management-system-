<?php

namespace Tests\Feature;

use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_login_accepts_plain_text_legacy_password_and_rehashes_it(): void
    {
        $admin = Admin::create([
            'name' => 'Primary Admin',
            'username' => 'john.perera',
            'email' => 'john@example.com',
            'password' => 'password',
        ]);

        $response = $this->post('/admin/login', [
            'username' => 'john.perera',
            'password' => 'password',
        ]);

        $response->assertRedirect('/admin');
        $this->assertTrue(session()->has('admin_id'));
        $this->assertTrue(Hash::check('password', $admin->fresh()->password));
    }

    public function test_admin_login_accepts_email_address_instead_of_username(): void
    {
        Admin::create([
            'name' => 'Primary Admin',
            'username' => 'superadmin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
        ]);

        $response = $this->post('/admin/login', [
            'username' => 'admin@example.com',
            'password' => 'password',
        ]);

        $response->assertRedirect('/admin');
        $this->assertTrue(session()->has('admin_id'));
    }
}
