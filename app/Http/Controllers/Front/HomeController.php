<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use App\Models\Package;
use App\Models\Testimonial;

class HomeController extends Controller
{
    public function index()
    {
        $packages = Package::latest()->take(3)->get();

        $testimonials = Testimonial::where('is_published', true)
            ->orderBy('sort_order')
            ->take(3)
            ->get();

        return view('front.home', compact('packages', 'testimonials'));
    }

    public function about()
    {
        return view('front.about');
    }

    public function faq()
    {
        $faqs = Faq::where('is_published', true)
            ->orderBy('sort_order')
            ->get();

        // Group by category (ungrouped fall under "Umum") while preserving
        // the global sort_order within each category.
        $grouped = $faqs->groupBy(fn (Faq $faq): string => $faq->category ?? 'Umum');

        return view('front.faq', compact('faqs', 'grouped'));
    }
}
