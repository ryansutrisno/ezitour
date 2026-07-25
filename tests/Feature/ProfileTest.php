<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

/**
 * Feature: user-profile.
 *
 * Covers the MVP profile page: auth gating, profile info + password updates,
 * validation, and the latent `phone` column on the users table.
 */
class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_profile(): void
    {
        $this->get(route('front.profile.edit'))
            ->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_view_profile(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('front.profile.edit'))
            ->assertOk()
            ->assertSee('Profil Saya');
    }

    public function test_user_can_update_profile_info(): void
    {
        $user = User::factory()->create(['name' => 'Old Name']);

        $this->actingAs($user)
            ->put(route('front.profile.update'), [
                'name' => 'Budi Baru',
                'email' => 'budi.baru@example.com',
                'phone' => '0812 3456 7890',
            ])
            ->assertRedirect(route('front.profile.edit'));

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Budi Baru',
            'email' => 'budi.baru@example.com',
            'phone' => '0812 3456 7890',
        ]);
    }

    public function test_user_can_change_password(): void
    {
        $user = User::factory()->create(['password' => Hash::make('old-password')]);

        $this->actingAs($user)
            ->put(route('front.profile.password'), [
                'current_password' => 'old-password',
                'password' => 'new-strong-password',
                'password_confirmation' => 'new-strong-password',
            ])
            ->assertRedirect(route('front.profile.edit'));

        $this->assertTrue(Hash::check('new-strong-password', $user->fresh()->password));
        $this->assertFalse(Hash::check('old-password', $user->fresh()->password));
    }

    public function test_password_update_rejects_wrong_current(): void
    {
        $user = User::factory()->create(['password' => Hash::make('correct-password')]);

        $response = $this->actingAs($user)
            ->from(route('front.profile.edit'))
            ->put(route('front.profile.password'), [
                'current_password' => 'wrong-current-password',
                'password' => 'new-strong-password',
                'password_confirmation' => 'new-strong-password',
            ]);

        $response->assertRedirect(route('front.profile.edit'));
        $response->assertSessionHasErrors('current_password');

        // Password must not have changed.
        $this->assertTrue(Hash::check('correct-password', $user->fresh()->password));
    }

    public function test_profile_update_validates_email_unique(): void
    {
        User::factory()->create(['email' => 'taken@example.com']);
        $user = User::factory()->create(['email' => 'mine@example.com']);

        $response = $this->actingAs($user)
            ->from(route('front.profile.edit'))
            ->put(route('front.profile.update'), [
                'name' => $user->name,
                'email' => 'taken@example.com',
                'phone' => null,
            ]);

        $response->assertRedirect(route('front.profile.edit'));
        $response->assertSessionHasErrors('email');
    }

    public function test_phone_field_now_exists_on_users_table(): void
    {
        // Proves the latent CheckoutController::register() bug is fixed: the
        // `phone` column now exists and is fillable, so registrations persist
        // the phone number instead of silently dropping it.
        $this->assertTrue(Schema::hasColumn('users', 'phone'));

        $user = User::create([
            'name' => 'Phone Check',
            'email' => 'phone.check@example.com',
            'password' => Hash::make('secret'),
            'phone' => '+62 812 0000 1111',
        ]);

        $this->assertSame('+62 812 0000 1111', $user->fresh()->phone);
    }
}
