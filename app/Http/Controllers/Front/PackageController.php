<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Package;
use Illuminate\Http\Request;

class PackageController extends Controller
{
    public function index(Request $request)
    {
        $query = Package::query();

        if ($request->filled('keyword')) {
            $query->where('name', 'like', '%' . $request->keyword . '%')
                  ->orWhere('description', 'like', '%' . $request->keyword . '%');
        }

        $packages = $query->latest()->paginate(9);

        return view('front.packages.index', compact('packages'));
    }

    public function show($slug)
    {
        $package = Package::where('slug', $slug)
            ->with(['destinations', 'items.destination']) // Load destinations and pivot data
            ->firstOrFail();

        return view('front.packages.show', compact('package'));
    }
}
