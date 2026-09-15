<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Tests for post-login redirect destinations by role.
 *
 * Admins are sent to the Filament panel, travelers to the customer
 * dashboard (booking history / "Riwayat Pesanan").
 */
class LoginRedirectTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_is_redirected_to_admin_panel(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->post(route('login.store'), [
            'email' => $admin->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticatedAs($admin);
        $response->assertRedirect(route('filament.admin.pages.dashboard'));
    }

    public function test_customer_is_redirected_to_dashboard(): void
    {
        $customer = User::factory()->create(['role' => 'user']);

        $response = $this->post(route('login.store'), [
            'email' => $customer->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticatedAs($customer);
        $response->assertRedirect(route('dashboard.index'));
    }

    public function test_user_with_default_role_is_redirected_to_dashboard(): void
    {
        // The `role` column is NOT NULL and defaults to 'user', so a user created
        // without an explicit role must never be treated as an admin.
        $customer = User::factory()->create();

        $this->assertSame('user', $customer->fresh()->role);

        $response = $this->post(route('login.store'), [
            'email' => $customer->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticatedAs($customer);
        $response->assertRedirect(route('dashboard.index'));
    }

    public function test_invalid_credentials_do_not_authenticate(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $response = $this->from(route('login'))->post(route('login.store'), [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors('email');
    }
}
