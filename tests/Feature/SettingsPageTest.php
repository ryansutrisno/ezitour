<?php

namespace Tests\Feature;

use App\Models\User;
use App\Settings\AboutSettings;
use App\Settings\ContactSettings;
use App\Settings\GeneralSettings;
use App\Settings\HomeSettings;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Feature: filament-settings
 *
 * Verifies that the spatie/laravel-settings groups hydrate from the seeded
 * defaults and that the Filament admin settings pages are reachable for
 * authenticated admins.
 */
class SettingsPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeded_general_settings_hydrate(): void
    {
        $settings = app(GeneralSettings::class);

        $this->assertSame('EziTour', $settings->siteName);
        $this->assertNotEmpty($settings->footerTagline);
    }

    public function test_seeded_home_settings_hydrate(): void
    {
        $settings = app(HomeSettings::class);

        $this->assertSame('500+', $settings->statDestinations);
        $this->assertSame('Liburan Impian,', $settings->heroHeadline);
        $this->assertSame('Tanpa Ribet.', $settings->heroHeadlineAccent);
    }

    public function test_seeded_contact_settings_hydrate(): void
    {
        $settings = app(ContactSettings::class);

        $this->assertSame('support@ezitour.com', $settings->email);
        $this->assertSame('+62 851 0326 3777', $settings->phone);
    }

    public function test_seeded_about_settings_hydrate_with_mission_points(): void
    {
        $settings = app(AboutSettings::class);

        $this->assertSame('2019', $settings->foundedYear);
        $this->assertIsArray($settings->missionPoints);
        $this->assertCount(3, $settings->missionPoints);
        $this->assertArrayHasKey('point', $settings->missionPoints[0]);
    }

    public function test_settings_pages_require_authentication(): void
    {
        $pages = [
            '/admin/manage-general-settings',
            '/admin/manage-home-settings',
            '/admin/manage-contact-settings',
            '/admin/manage-about-settings',
        ];

        foreach ($pages as $page) {
            $this->get($page)->assertRedirect('/admin/login');
        }
    }

    public function test_authenticated_admin_can_view_settings_pages(): void
    {
        $this->withoutExceptionHandling();

        $user = User::factory()->create();

        $pages = [
            '/admin/manage-general-settings',
            '/admin/manage-home-settings',
            '/admin/manage-contact-settings',
            '/admin/manage-about-settings',
        ];

        foreach ($pages as $page) {
            $this->actingAs($user)
                ->get($page)
                ->assertSuccessful();
        }
    }

    public function test_settings_pages_appear_under_pengaturan_navigation_group(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/manage-general-settings');

        $response->assertSee('Pengaturan');
        $response->assertSee('Simpan Perubahan');
    }
}
