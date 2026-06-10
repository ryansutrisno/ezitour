<?php

namespace App\Http\Controllers\Front;

use App\Exceptions\PaymentException;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Services\PaymentErrorHandler;
use App\Services\PaymentLogger;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

/**
 * PaymentController - Handle payment initiation and callbacks
 * 
 * This controller handles:
 * - Payment initiation (create Snap Token)
 * - Payment finish callback
 * - Payment unfinish callback
 * - Payment error callback
 * 
 * Requirements: 1.2, 1.3, 1.4, 1.5, 3.5
 */
class PaymentController extends Controller
{
    /**
     * PaymentService instance
     */
    protected PaymentService $paymentService;

    /**
     * Create a new PaymentController instance.
     *
     * @param PaymentService $paymentService
     */
    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    /**
     * Create payment and get Snap Token for a booking
     * 
     * Requirements: 1.2, 1.3
     * - Display "Pay Now" button for pending booking
     * - Request Snap Token from Midtrans API
     *
     * @param Request $request
     * @param Booking $booking
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function create(Request $request, Booking $booking)
    {
        // Validate booking ownership
        if (!$this->validateBookingOwnership($booking)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses ke booking ini.',
                ], 403);
            }
            
            return redirect()->route('dashboard.index')
                ->with('error', 'Anda tidak memiliki akses ke booking ini.');
        }

        // Check if booking is already paid
        if ($booking->isPaid()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Booking ini sudah dibayar.',
                ], 400);
            }
            
            return redirect()->route('dashboard.index')
                ->with('error', 'Booking ini sudah dibayar.');
        }

        try {
            // Create payment and get Snap Token
            $result = $this->paymentService->createPayment($booking);

            Log::info('Payment initiated', [
                'booking_id' => $booking->id,
                'user_id' => Auth::id(),
                'order_id' => $result['transaction']->order_id,
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'snap_token' => $result['snap_token'],
                    'order_id' => $result['transaction']->order_id,
                ]);
            }

            // For non-AJAX requests, return view with Snap Token
            return view('front.payments.pay', [
                'booking' => $booking->load(['package', 'user']),
                'snapToken' => $result['snap_token'],
                'orderId' => $result['transaction']->order_id,
                'clientKey' => config('midtrans.client_key'),
            ]);

        } catch (PaymentException $e) {
            // PaymentException already logs the error
            // Return user-friendly error response
            if ($request->expectsJson()) {
                return PaymentErrorHandler::jsonResponse($e);
            }

            return PaymentErrorHandler::redirectWithError($e, 'dashboard.index');

        } catch (\Exception $e) {
            Log::error('Payment creation failed', [
                'booking_id' => $booking->id,
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
            ]);

            // Return user-friendly error response
            if ($request->expectsJson()) {
                return PaymentErrorHandler::jsonResponse($e);
            }

            return PaymentErrorHandler::redirectWithError($e, 'dashboard.index');
        }
    }

    /**
     * Handle payment finish callback from Midtrans
     * 
     * This is called when customer completes payment on Midtrans page
     * Requirements: 1.4
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function finish(Request $request)
    {
        $orderId = $request->get('order_id');
        $transactionStatus = $request->get('transaction_status');
        
        Log::info('Payment finish callback received', [
            'order_id' => $orderId,
            'transaction_status' => $transactionStatus,
            'user_id' => Auth::id(),
        ]);

        // For development: Check payment status from Midtrans API
        // This helps when webhook is not accessible (localhost)
        if ($orderId && in_array($transactionStatus, ['capture', 'settlement'])) {
            try {
                Log::info('Checking payment status from Midtrans API', [
                    'order_id' => $orderId,
                ]);
                
                $this->paymentService->checkPaymentStatus($orderId);
                
                Log::info('Payment status checked and updated successfully', [
                    'order_id' => $orderId,
                ]);
            } catch (\Exception $e) {
                Log::warning('Failed to check payment status on finish callback', [
                    'order_id' => $orderId,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        // Determine message based on transaction status
        $message = $this->getFinishMessage($transactionStatus);

        return redirect()->route('dashboard.index')
            ->with('success', $message);
    }

    /**
     * Handle payment unfinish callback from Midtrans
     * 
     * This is called when customer closes payment page without completing
     * Requirements: 1.4
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function unfinish(Request $request)
    {
        $orderId = $request->get('order_id');
        
        Log::info('Payment unfinish callback received', [
            'order_id' => $orderId,
            'user_id' => Auth::id(),
        ]);

        return redirect()->route('dashboard.index')
            ->with('warning', 'Pembayaran belum selesai. Anda dapat melanjutkan pembayaran kapan saja.');
    }

    /**
     * Handle payment error callback from Midtrans
     * 
     * This is called when payment fails or encounters an error
     * Requirements: 1.4
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function error(Request $request)
    {
        $orderId = $request->get('order_id');
        $statusCode = $request->get('status_code');
        $statusMessage = $request->get('status_message');
        
        Log::warning('Payment error callback received', [
            'order_id' => $orderId,
            'status_code' => $statusCode,
            'status_message' => $statusMessage,
            'user_id' => Auth::id(),
        ]);

        return redirect()->route('dashboard.index')
            ->with('error', 'Pembayaran gagal. Silakan coba lagi atau hubungi customer service.');
    }

    /**
     * Validate that the authenticated user owns the booking
     *
     * @param Booking $booking
     * @return bool
     */
    protected function validateBookingOwnership(Booking $booking): bool
    {
        return $booking->user_id === Auth::id();
    }

    /**
     * Get appropriate message based on transaction status
     *
     * @param string|null $transactionStatus
     * @return string
     */
    protected function getFinishMessage(?string $transactionStatus): string
    {
        return match ($transactionStatus) {
            'capture', 'settlement' => 'Pembayaran berhasil! Terima kasih atas pesanan Anda.',
            'pending' => 'Pembayaran sedang diproses. Kami akan mengkonfirmasi setelah pembayaran diterima.',
            'deny' => 'Pembayaran ditolak. Silakan coba metode pembayaran lain.',
            'cancel' => 'Pembayaran dibatalkan.',
            'expire' => 'Pembayaran telah kedaluwarsa. Silakan buat pembayaran baru.',
            default => 'Pembayaran telah diproses. Silakan cek status booking Anda.',
        };
    }

    /**
     * Retry payment for a booking with failed/expired transaction
     * 
     * Requirements: 8.1, 8.2, 8.3, 8.4, 8.5
     * - Allow creating new payment attempt after failure
     * - Generate new Snap Token
     * - Create new Transaction record
     * - Use the same Booking
     * - Mark previous failed Transaction as superseded
     *
     * @param Request $request
     * @param Booking $booking
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function retry(Request $request, Booking $booking)
    {
        // Validate booking ownership
        if (!$this->validateBookingOwnership($booking)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses ke booking ini.',
                ], 403);
            }
            
            return redirect()->route('dashboard.index')
                ->with('error', 'Anda tidak memiliki akses ke booking ini.');
        }

        // Check if booking is already paid
        if ($booking->isPaid()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Booking ini sudah dibayar.',
                ], 400);
            }
            
            return redirect()->route('dashboard.index')
                ->with('error', 'Booking ini sudah dibayar.');
        }

        // Check if booking can retry payment
        if (!$this->paymentService->canRetryPayment($booking)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak dapat mengulang pembayaran untuk booking ini.',
                ], 400);
            }
            
            return redirect()->route('dashboard.index')
                ->with('error', 'Tidak dapat mengulang pembayaran untuk booking ini.');
        }

        try {
            // Retry payment and get new Snap Token
            $result = $this->paymentService->retryPayment($booking);

            Log::info('Payment retry initiated', [
                'booking_id' => $booking->id,
                'user_id' => Auth::id(),
                'order_id' => $result['transaction']->order_id,
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'snap_token' => $result['snap_token'],
                    'order_id' => $result['transaction']->order_id,
                ]);
            }

            // For non-AJAX requests, return view with Snap Token
            return view('front.payments.pay', [
                'booking' => $booking->load(['package', 'user']),
                'snapToken' => $result['snap_token'],
                'orderId' => $result['transaction']->order_id,
                'clientKey' => config('midtrans.client_key'),
                'isRetry' => true,
            ]);

        } catch (PaymentException $e) {
            // PaymentException already logs the error
            // Return user-friendly error response
            if ($request->expectsJson()) {
                return PaymentErrorHandler::jsonResponse($e);
            }

            return PaymentErrorHandler::redirectWithError($e, 'dashboard.index');

        } catch (\Exception $e) {
            Log::error('Payment retry failed', [
                'booking_id' => $booking->id,
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
            ]);

            // Return user-friendly error response
            if ($request->expectsJson()) {
                return PaymentErrorHandler::jsonResponse($e);
            }

            return PaymentErrorHandler::redirectWithError($e, 'dashboard.index');
        }
    }
}
