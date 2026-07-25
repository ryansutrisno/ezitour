<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Package;

class HomeController extends Controller
{
    public function index()
    {
        $packages = Package::latest()->take(3)->get();

        return view('front.home', compact('packages'));
    }

    public function about()
    {
        return view('front.about');
    }
}
