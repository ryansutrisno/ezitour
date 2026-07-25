<?php

namespace App\Http\Controllers\Front;

use App\Exceptions\InvalidSignatureException;
use App\Exceptions\NotificationProcessingException;
use App\Http\Controllers\Controller;
use App\Services\PaymentLogger;
use App\Services\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * MidtransNotificationController - Handle webhook notifications from Midtrans
 *
 * This controller receives payment status notifications from Midtrans
 * and delegates processing to PaymentService.
 *
 * Requirements: 4.1, 4.8, 11.2
 */
class MidtransNotificationController extends Controller
{
    /**
     * PaymentService instance
     */
    protected PaymentService $paymentService;

    /**
     * Create a new controller instance.
     */
    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    /**
     * Handle incoming payment notification from Midtrans webhook
     *
     * This endpoint receives POST requests from Midtrans containing
     * payment status updates. The notification is verified and processed
     * to update transaction and booking status.
     *
     * Requirements:
     * - 4.1: Verify notification signature
     * - 4.8: Respond with HTTP 200 to acknowledge receipt
     * - 11.2: Log notification processing failures without breaking webhook
     */
    public function handle(Request $request): JsonResponse
    {
        // Get raw notification data from request
        $notification = $request->all();

        try {
            // Delegate processing to PaymentService
            // This handles:
            // - Signature verification
            // - Status mapping
            // - Transaction update
            // - Booking status update
            // - Comprehensive logging
            $this->paymentService->processNotification($notification);

            // Return HTTP 200 to acknowledge receipt (Requirement 4.8)
            return response()->json([
                'status' => 'success',
                'message' => 'Notification processed successfully',
            ], 200);

        } catch (InvalidSignatureException $e) {
            // Invalid signature - already logged by PaymentService/MidtransClient
            // Return 403 but don't break the webhook
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid signature',
            ], 403);

        } catch (NotificationProcessingException $e) {
            // Processing error - already logged by exception
            // Return 200 to prevent Midtrans from retrying
            return response()->json([
                'status' => 'error',
                'message' => 'Notification processing failed',
            ], 200);

        } catch (\Exception $e) {
            // Handle unexpected exceptions gracefully
            // Log with full context (Requirement 11.2)
            PaymentLogger::logNotificationProcessingFailure($notification, $e);

            // Return 200 to acknowledge receipt even on error
            // This prevents Midtrans from continuously retrying
            // The error is logged for manual investigation
            return response()->json([
                'status' => 'error',
                'message' => 'Notification processing failed',
            ], 200);
        }
    }
}
