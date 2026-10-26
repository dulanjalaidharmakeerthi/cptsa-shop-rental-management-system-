<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Payment;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccountantAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_an_accountant_user(): void
    {
        $admin = Admin::create([
            'name' => 'Primary Admin',
            'username' => 'primary-admin',
            'email' => 'admin@example.com',
            'password' => 'password',
        ]);

        $this->withSession(['admin_id' => $admin->id])
            ->post(route('admin.users.store'), [
                'name' => 'Accounts User',
                'email' => 'accounts@example.com',
                'phone' => '123456789',
                'position' => 'Accountant',
                'password' => 'password123',
                'password_confirmation' => 'password123',
            ])
            ->assertRedirect(route('admin.users.create'));

        $this->assertDatabaseHas('users', [
            'email' => 'accounts@example.com',
            'position' => 'Accountant',
        ]);
    }

    public function test_accountant_login_redirects_to_the_manager_dashboard(): void
    {
        User::factory()->create([
            'email' => 'accounts@example.com',
            'position' => 'Accountant',
            'password' => 'password123',
        ]);

        $this->post(route('login'), [
            'username' => 'accounts@example.com',
            'password' => 'password123',
        ])->assertRedirect('/managedashboard');
    }

    public function test_accountant_can_view_dashboard_payment_list_and_history(): void
    {
        $accountant = User::factory()->create(['position' => 'Accountant']);
        $this->actingAs($accountant);

        $this->get(route('managedashboard'))->assertOk();
        $this->get(route('accountant.payments.index'))->assertOk();
        $this->get(route('history'))->assertOk();
    }

    public function test_accountant_cannot_access_other_management_or_payment_recording_routes(): void
    {
        $accountant = User::factory()->create(['position' => 'Accountant']);
        $this->actingAs($accountant);

        $this->get('/shops/manage')->assertForbidden();
        $this->get('/payments')->assertForbidden();
        $this->post('/payments/record')->assertForbidden();
        $this->get(route('history.export'))->assertForbidden();
    }

    public function test_accountant_can_edit_an_existing_payment(): void
    {
        $shop = Shop::create([
            'shop_number' => 'A-1',
            'shop_name' => 'Test Shop',
            'shop_owner_name' => 'Test Owner',
            'monthly_rent' => 1000,
            'record_year' => now()->year,
        ]);
        $payment = Payment::create([
            'shop_id' => $shop->id,
            'amount' => 1000,
            'interest' => 0,
            'receipt_number' => 'OLD-1',
            'paid_at' => now(),
        ]);
        $accountant = User::factory()->create(['position' => 'Accountant']);
        $this->actingAs($accountant);

        $this->get(route('accountant.payments.edit', $payment->id))->assertOk();

        $this->put(route('accountant.payments.update', $payment->id), [
            'shop_id' => $shop->id,
            'amount' => 950,
            'interest' => 25,
            'receipt_number' => 'UPDATED-1',
            'paid_at' => now()->toDateString(),
        ])->assertRedirect(route('accountant.payments.index'));

        $this->assertDatabaseHas('payments', [
            'id' => $payment->id,
            'amount' => 950,
            'receipt_number' => 'UPDATED-1',
        ]);
    }
}
