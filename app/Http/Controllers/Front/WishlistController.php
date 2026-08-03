<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Package;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WishlistController extends Controller
{
    /**
     * Display the authenticated user's saved packages (wishlist).
     */
    public function index(Request $request): View
    {
        $packages = $request->user()
            ->favorites()
            ->latest()
            ->paginate(9);

        return view('front.wishlist.index', compact('packages'));
    }

    /**
     * Toggle a package in/out of the authenticated user's wishlist.
     *
     * Returns JSON for the AJAX heart-toggle button on package cards and
     * the package detail page. Responds with 404 when the slug is unknown.
     */
    public function toggle(Request $request, string $slug): JsonResponse
    {
        $package = Package::where('slug', $slug)->firstOrFail();

        $attached = $request->user()
            ->favorites()
            ->toggle([$package->id]);

        $isFavorited = in_array($package->id, $attached['attached'], true);

        return response()->json([
            'is_favorited' => $isFavorited,
            'count' => $request->user()->favorites()->count(),
            'message' => $isFavorited
                ? 'Ditambahkan ke wishlist'
                : 'Dihapus dari wishlist',
        ]);
    }

    /**
     * Remove a package from the authenticated user's wishlist.
     */
    public function destroy(Request $request, string $slug): RedirectResponse
    {
        $package = Package::where('slug', $slug)->firstOrFail();

        $request->user()->favorites()->detach([$package->id]);

        return redirect()
            ->back()
            ->with('success', 'Paket dihapus dari wishlist');
    }
}
