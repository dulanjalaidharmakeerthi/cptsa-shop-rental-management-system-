<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ProfileUpdateTest extends TestCase
{

    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('phone')->nullable();
            $table->string('position')->nullable();
            $table->boolean('is_blocked')->default(false);
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('actor_id')->nullable();
            $table->string('actor_type')->nullable();
            $table->string('actor_name')->nullable();
            $table->string('actor_role')->nullable();
            $table->string('actor_contact')->nullable();
            $table->string('action');
            $table->string('auditable_type');
            $table->unsignedBigInteger('auditable_id')->nullable();
            $table->string('auditable_name')->nullable();
            $table->text('description')->nullable();
            $table->text('old_values')->nullable();
            $table->text('new_values')->nullable();
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }

    protected function tearDown(): void
    {
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('users');

        parent::tearDown();
    }

    public function test_authenticated_user_can_update_their_profile(): void
    {
        $user = User::factory()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '0771234567',
            'position' => 'Manager',
        ]);

        $this->actingAs($user);

        $response = $this->put('/profile', [
            'name' => 'John Smith',
            'email' => 'john.smith@example.com',
            'phone' => '0777654321',
            'position' => 'Senior Manager',
        ]);

        $response->assertRedirect(route('dashboard.profile.show'));
        $response->assertSessionHas('success', 'Profile updated successfully.');

        $user->refresh();

        $this->assertSame('John Smith', $user->name);
        $this->assertSame('john.smith@example.com', $user->email);
        $this->assertSame('0777654321', $user->phone);
        $this->assertSame('Manager', $user->position);
    }

    public function test_authenticated_user_can_change_password_with_current_password(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('currentpassword'),
        ]);

        $this->actingAs($user);

        $response = $this->put('/profile/password', [
            'current_password' => 'currentpassword',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $response->assertRedirect(route('dashboard.profile.show'));
        $response->assertSessionHas('success', 'Password changed successfully.');
        $this->assertTrue(Hash::check('newpassword123', $user->fresh()->password));
    }

    public function test_user_cannot_change_password_without_correct_current_password(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('currentpassword'),
        ]);

        $this->actingAs($user);

        $response = $this->from('/profile/password')->put('/profile/password', [
            'current_password' => 'incorrectpassword',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $response->assertSessionHasErrors('current_password');
        $this->assertTrue(Hash::check('currentpassword', $user->fresh()->password));
    }

    public function test_profile_and_password_options_are_available_to_accountants(): void
    {
        $user = User::factory()->create(['position' => 'Accountant']);

        $this->actingAs($user);

        $this->get('/profile')->assertOk()->assertSee('Edit Profile')->assertSee('Change Password');
        $this->get('/profile/edit')->assertOk();
        $this->get('/profile/password')->assertOk()->assertSee('Update Password');
    }
}
