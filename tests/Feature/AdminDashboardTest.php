<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Guards the Filament admin panel against render-time failures.
 *
 * Admins are now redirected to /admin after login, so the panel dashboard is
 * actually rendered for the first time. A dashboard widget querying a missing
 * Eloquent relation (e.g. Package::bookings()) used to take down the whole
 * page, so these tests render the real panel instead of only checking routes.
 */
class AdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    /**
     * The full dashboard must render, including its aggregate widgets.
     */
    public function test_admin_can_render_the_dashboard(): void
    {
        /** @var User $admin */
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->get('/admin')
            ->assertOk();
    }

    /**
     * Guests hitting the panel get the Filament login screen, not a 500.
     */
    public function test_guest_is_redirected_to_the_panel_login(): void
    {
        $this->get('/admin')
            ->assertRedirect(route('filament.admin.auth.login'));
    }

    /**
     * Travelers must stay out of the panel entirely.
     */
    public function test_customer_cannot_access_the_admin_panel(): void
    {
        /** @var User $customer */
        $customer = User::factory()->create();

        $this->actingAs($customer)
            ->get('/admin')
            ->assertForbidden();
    }
}
