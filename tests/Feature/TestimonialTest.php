<?php

namespace Tests\Feature;

use App\Models\Testimonial;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Feature: Testimonial CRUD front-end rendering.
 *
 * Verifies that published testimonials render on the home page, unpublished
 * ones are hidden, and the ordering follows the `sort_order` column.
 */
class TestimonialTest extends TestCase
{
    use RefreshDatabase;

    public function test_published_testimonials_appear_on_home_page(): void
    {
        Testimonial::create([
            'name' => 'Budi Santoso',
            'location' => 'Jakarta',
            'quote' => 'Pengalaman liburan yang sangat menyenangkan bersama EziTour.',
            'rating' => 5,
            'is_published' => true,
            'sort_order' => 1,
        ]);

        $this->get(route('front.home'))
            ->assertOk()
            ->assertSee('Budi Santoso')
            ->assertSee('Pengalaman liburan yang sangat menyenangkan');
    }

    public function test_unpublished_testimonials_do_not_appear_on_home_page(): void
    {
        Testimonial::create([
            'name' => 'Hidden Traveler',
            'location' => 'Surabaya',
            'quote' => 'Ini testimonial yang disembunyikan dari publik.',
            'rating' => 4,
            'is_published' => false,
            'sort_order' => 1,
        ]);

        $this->get(route('front.home'))
            ->assertOk()
            ->assertDontSee('Hidden Traveler')
            ->assertDontSee('Ini testimonial yang disembunyikan');
    }

    public function test_testimonials_are_ordered_by_sort_order(): void
    {
        Testimonial::create([
            'name' => 'Charlie Second', 'location' => 'Bandung', 'quote' => 'quote-charlie',
            'rating' => 5, 'is_published' => true, 'sort_order' => 2,
        ]);
        Testimonial::create([
            'name' => 'Alpha First', 'location' => 'Medan', 'quote' => 'quote-alpha',
            'rating' => 5, 'is_published' => true, 'sort_order' => 1,
        ]);

        $response = $this->get(route('front.home'))->assertOk();

        $alphaPos = mb_strpos($response->content(), 'Alpha First');
        $charliePos = mb_strpos($response->content(), 'Charlie Second');

        $this->assertNotFalse($alphaPos);
        $this->assertNotFalse($charliePos);
        $this->assertLessThan(
            $charliePos,
            $alphaPos,
            'Alpha (sort_order 1) should render before Charlie (sort_order 2).',
        );
    }
}
