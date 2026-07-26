<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Mail\BookingConfirmed;
use App\Models\Booking;
use App\Models\Package;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class BookingController extends Controller
{
    public function store(Request $request, $slug)
    {
        $package = Package::where('slug', $slug)->firstOrFail();

        $validated = $request->validate([
            'travel_date' => 'required|date|after:today',
            'participants' => 'required|integer|min:1',
            'pickup_location' => 'required|string|max:500',
        ]);

        // Calculate total amount (Price * Participants)
        // Assumption: total_price in Package is per pax.
        $totalAmount = $package->total_price * $validated['participants'];

        $booking = Booking::create([
            'user_id' => Auth::id(),
            'package_id' => $package->id,
            'travel_date' => $validated['travel_date'],
            'total_amount' => $totalAmount,
            'pickup_location' => $validated['pickup_location'],
            'status' => 'pending',
        ]);

        // Send confirmation email (best-effort — never block the booking flow).
        try {
            Mail::to($booking->user)->send(new BookingConfirmed($booking));
        } catch (\Throwable $e) {
            Log::warning('Failed to send BookingConfirmed email', [
                'booking_id' => $booking->id,
                'error' => $e->getMessage(),
            ]);
        }

        return redirect()->route('dashboard.index')->with('success', 'Booking berhasil dibuat! Silakan tunggu konfirmasi admin.');
    }
}
