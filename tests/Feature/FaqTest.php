<?php

namespace Tests\Feature;

use App\Models\Faq;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Feature: FAQ public page rendering.
 *
 * Verifies the /faq route loads, published FAQs (question + answer + category)
 * are visible, and unpublished FAQs are excluded.
 */
class FaqTest extends TestCase
{
    use RefreshDatabase;

    public function test_faq_page_loads_successfully(): void
    {
        $this->get(route('front.faq'))->assertOk();
    }

    public function test_published_faqs_appear_on_faq_page(): void
    {
        Faq::create([
            'question' => 'Bagaimana cara melakukan pembayaran?',
            'answer' => 'Pembayaran melalui Midtrans Snap sangat mudah dan aman.',
            'category' => 'Pembayaran',
            'is_published' => true,
            'sort_order' => 1,
        ]);

        $this->get(route('front.faq'))
            ->assertOk()
            ->assertSee('Bagaimana cara melakukan pembayaran?')
            ->assertSee('Pembayaran melalui Midtrans Snap sangat mudah dan aman.')
            ->assertSee('Pembayaran');
    }

    public function test_unpublished_faqs_do_not_appear_on_faq_page(): void
    {
        Faq::create([
            'question' => 'Pertanyaan rahasia internal?',
            'answer' => 'Jawaban yang sama sekali tidak boleh tampil ke publik.',
            'category' => 'Umum',
            'is_published' => false,
            'sort_order' => 1,
        ]);

        $this->get(route('front.faq'))
            ->assertOk()
            ->assertDontSee('Pertanyaan rahasia internal?')
            ->assertDontSee('Jawaban yang sama sekali tidak boleh tampil');
    }

    public function test_faq_page_shows_empty_state_when_no_faqs(): void
    {
        $this->get(route('front.faq'))
            ->assertOk()
            ->assertSee('Belum ada pertanyaan yang ditampilkan');
    }
}
