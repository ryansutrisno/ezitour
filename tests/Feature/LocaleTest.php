<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Feature: locale / i18n Phase 1.
 *
 * Covers: ID default locale, /en/ prefix routing, locale-switch endpoint,
 * user preference persistence, and invalid-locale rejection.
 */
class LocaleTest extends TestCase
{
    use RefreshDatabase;

    // -----------------------------------------------------------------------
    // Routing
    // -----------------------------------------------------------------------

    public function test_indonesian_is_default_locale(): void
    {
        $this->get(route('front.home'))
            ->assertOk()
            ->assertSee(__('front.nav_home', [], 'id'), false);
    }

    public function test_english_prefix_sets_locale(): void
    {
        // The /en/ route group is registered under the `en.` namespace.
        $this->get('/en/')
            ->assertOk()
            ->assertSee(__('front.nav_home', [], 'en'), false);
    }

    public function test_unknown_locale_prefix_is_not_registered(): void
    {
        // Only 'en' is registered as a prefix — /fr/ should 404.
        $this->get('/fr/')->assertNotFound();
    }

    // -----------------------------------------------------------------------
    // Locale switch endpoint
    // -----------------------------------------------------------------------

    public function test_locale_switch_persists_to_session(): void
    {
        $this->get(route('front.locale.switch', ['locale' => 'en']))
            ->assertRedirect()
            ->assertSessionHas('locale', 'en');
    }

    public function test_locale_switch_persists_to_user_when_authenticated(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('front.locale.switch', ['locale' => 'en']));

        $this->assertSame('en', $user->fresh()->locale);
    }

    public function test_invalid_locale_switch_returns_404(): void
    {
        $this->get(route('front.locale.switch', ['locale' => 'fr']))
            ->assertNotFound();
    }

    // -----------------------------------------------------------------------
    // Navbar toggle
    // -----------------------------------------------------------------------

    public function test_locale_toggle_rendered_in_navbar(): void
    {
        $this->get(route('front.home'))
            ->assertOk()
            ->assertSee('ID', false)
            ->assertSee('EN', false);
    }
}
