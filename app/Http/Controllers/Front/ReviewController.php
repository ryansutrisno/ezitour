<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Package;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    /**
     * Store a new review for a package.
     *
     * Only users with a paid booking for this package — and who have not
     * already reviewed it — may submit a review. Authorization is enforced
     * via ReviewPolicy::create(), with a hard guarantee by the reviews table
     * unique(user_id, package_id) constraint.
     */
    public function store(Request $request, string $slug): RedirectResponse
    {
        $package = Package::where('slug', $slug)->firstOrFail();

        $validated = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'max:1000'],
        ], [
            'rating.required' => 'Rating wajib dipilih.',
            'rating.integer' => 'Rating harus berupa angka.',
            'rating.min' => 'Rating minimal 1 bintang.',
            'rating.max' => 'Rating maksimal 5 bintang.',
            'comment.max' => 'Komentar maksimal 1000 karakter.',
        ]);

        $this->authorize('create', [Review::class, $package]);

        Review::create([
            'user_id' => Auth::id(),
            'package_id' => $package->id,
            'rating' => $validated['rating'],
            'comment' => $validated['comment'] ?? null,
            'is_approved' => true,
        ]);

        return redirect()
            ->route('front.packages.show', $package->slug)
            ->with('success', 'Terima kasih atas review Anda!');
    }
}
