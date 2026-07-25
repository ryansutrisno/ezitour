<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Display the user's dashboard with booking history.
     *
     * Requirements: 6.1 - Display current payment status for bookings
     * Task 16: Add filter for paid/unpaid bookings
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $paymentFilter = $request->get('payment_status', 'all');

        $query = Booking::where('user_id', Auth::id())
            ->with([
                'package',
                'car',
                'driver',
                'transactions' => function ($query) {
                    $query->latest();
                },
                'latestTransaction',
            ]);

        // Apply payment status filter
        if ($paymentFilter === 'paid') {
            $query->where('status', 'paid');
        } elseif ($paymentFilter === 'unpaid') {
            $query->where('status', '!=', 'paid');
        }

        $bookings = $query->latest()->get();

        // Get counts for filter badges
        $allCount = Booking::where('user_id', Auth::id())->count();
        $paidCount = Booking::where('user_id', Auth::id())->where('status', 'paid')->count();
        $unpaidCount = Booking::where('user_id', Auth::id())->where('status', '!=', 'paid')->count();

        return view('front.dashboard.index', compact(
            'bookings',
            'paymentFilter',
            'allCount',
            'paidCount',
            'unpaidCount'
        ));
    }
}
