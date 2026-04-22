<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_page_renders(): void
    {
        $this->get('/login')->assertStatus(200);
    }

    public function test_register_page_renders(): void
    {
        $this->get('/register')->assertStatus(200);
    }

    public function test_forgot_password_page_renders(): void
    {
        $this->get('/forgot-password')->assertStatus(200);
    }

    public function test_buyer_can_register(): void
    {
        $this->post('/register', [
            'name'                  => 'Test Buyer',
            'email'                 => 'buyer@test.com',
            'phone'                 => '09123456789',
            'role'                  => 'buyer',
            'location'              => 'Cantilan',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
        ])->assertRedirect();

        $this->assertDatabaseHas('users', ['email' => 'buyer@test.com', 'role' => 'buyer']);
    }

    public function test_buyer_can_login(): void
    {
        $user = User::factory()->create(['role' => 'buyer', 'account_status' => 'approved']);

        $this->post('/login', [
            'email'    => $user->email,
            'password' => 'password',
        ])->assertRedirect();

        $this->assertAuthenticatedAs($user);
    }

    public function test_invalid_credentials_rejected(): void
    {
        $this->post('/login', [
            'email'    => 'nobody@test.com',
            'password' => 'wrongpassword',
        ])->assertSessionHasErrors('email');
    }

    public function test_guest_cannot_access_buyer_dashboard(): void
    {
        $this->get('/buyer/dashboard')->assertRedirect('/login');
    }

    public function test_guest_cannot_access_farmer_dashboard(): void
    {
        $this->get('/farmer/dashboard')->assertRedirect('/login');
    }

    public function test_guest_cannot_access_admin_dashboard(): void
    {
        $this->get('/admin/dashboard')->assertRedirect('/login');
    }

    public function test_buyer_cannot_access_admin_dashboard(): void
    {
        $buyer = User::factory()->create(['role' => 'buyer', 'account_status' => 'approved']);
        $this->actingAs($buyer)->get('/admin/dashboard')->assertForbidden();
    }

    public function test_farmer_cannot_access_admin_dashboard(): void
    {
        $farmer = User::factory()->create(['role' => 'farmer', 'account_status' => 'approved']);
        $this->actingAs($farmer)->get('/admin/dashboard')->assertForbidden();
    }

    public function test_user_can_logout(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)->post('/logout')->assertRedirect('/');
        $this->assertGuest();
    }
}
