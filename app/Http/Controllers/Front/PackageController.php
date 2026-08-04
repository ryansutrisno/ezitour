<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Package;
use Illuminate\Http\Request;

class PackageController extends Controller
{
    public function index(Request $request)
    {
        $filters = [
            'keyword' => $request->input('keyword'),
            'region' => $request->input('region'),
            'category' => $request->input('category'),
            'duration_min' => $request->input('duration_min'),
            'duration_max' => $request->input('duration_max'),
        ];

        $packages = Package::query()
            ->filter($filters)
            ->latest()
            ->paginate(9);

        $regions = Package::query()
            ->whereNotNull('region')
            ->whereNot('region', '')
            ->distinct()
            ->orderBy('region')
            ->pluck('region', 'region');

        $categories = Package::query()
            ->whereNotNull('category')
            ->whereNot('category', '')
            ->distinct()
            ->orderBy('category')
            ->pluck('category', 'category');

        $durationBuckets = [
            ['min' => 1, 'max' => 3, 'label' => '1-3 '.__('front.duration_days_suffix')],
            ['min' => 4, 'max' => 7, 'label' => '4-7 '.__('front.duration_days_suffix')],
            ['min' => 8, 'max' => 14, 'label' => '8+ '.__('front.duration_days_suffix')],
        ];

        return view('front.packages.index', compact('packages', 'filters', 'regions', 'categories', 'durationBuckets'));
    }

    public function show($slug)
    {
        $package = Package::where('slug', $slug)
            ->with(['destinations', 'items.destination']) // Load destinations and pivot data
            ->firstOrFail();

        return view('front.packages.show', compact('package'));
    }
}
