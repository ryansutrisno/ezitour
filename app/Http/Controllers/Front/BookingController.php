<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Mail\BookingConfirmed;
use App\Models\Booking;
use App\Models\Package;
use App\Services\PaymentService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

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

    /**
     * Show the booking detail page (owner-only).
     */
    public function show(Booking $booking): View
    {
        $this->authorizeOwner($booking);

        $booking->load([
            'package',
            'user',
            'transactions' => fn ($q) => $q->latest(),
            'latestTransaction',
            'car',
            'driver',
        ]);

        return view('front.bookings.show', compact('booking'));
    }

    /**
     * Download the booking's E-Ticket as a PDF (owner-only, paid bookings only).
     */
    public function ticket(Booking $booking): Response
    {
        $this->authorizeOwner($booking);

        if (! $booking->isPaid()) {
            return redirect()
                ->route('bookings.show', $booking)
                ->with('error', 'E-Ticket hanya tersedia untuk booking yang sudah dibayar.');
        }

        $booking->load(['package', 'user', 'latestTransaction']);

        $pdf = Pdf::loadView('pdf.ticket', compact('booking'));

        return $pdf->download('e-ticket-'.$booking->code.'.pdf');
    }

    /**
     * Cancel a pending booking (owner-only).
     */
    public function cancel(Request $request, Booking $booking, PaymentService $paymentService): RedirectResponse
    {
        $this->authorizeOwner($booking);

        $result = $paymentService->cancelBooking($booking);

        if ($result['success']) {
            return redirect()
                ->route('bookings.show', $booking)
                ->with('success', $result['message']);
        }

        return redirect()
            ->route('bookings.show', $booking)
            ->with('error', 'Gagal membatalkan: '.$result['message']);
    }

    /**
     * Ensure the authenticated user owns the booking, else 403.
     */
    protected function authorizeOwner(Booking $booking): void
    {
        if ($booking->user_id !== Auth::id()) {
            abort(403, 'Kamu tidak punya akses ke booking ini.');
        }
    }
}
