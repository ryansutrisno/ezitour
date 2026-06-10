<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

/**
 * PaymentLogger - Centralized logging service for payment operations
 * 
 * This service provides comprehensive logging for all payment-related activities
 * with consistent formatting and full context.
 * 
 * Requirements: 11.1, 11.2, 11.3, 11.4
 */
class PaymentLogger
{
    /**
     * Log channel name
     */
    protected const CHANNEL = 'payment';

    /**
     * Log payment creation attempt
     *
     * @param Booking $booking
     * @param string $orderId
     * @return void
     */
    public static function logPaymentCreationAttempt(Booking $booking, string $orderId): void
    {
        Log::channel(self::getChannel())->info('Payment creation initiated', [
            'event' => 'payment.creation.attempt',
            'booking_id' => $booking->id,
            'order_id' => $orderId,
            'amount' => $booking->total_amount,
            'user_id' => $booking->user_id,
            'package_id' => $booking->package_id,
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    /**
     * Log successful payment creation
     *
     * @param Booking $booking
     * @param Transaction $transaction
     * @return void
     */
    public static function logPaymentCreationSuccess(Booking $booking, Transaction $transaction): void
    {
        Log::channel(self::getChannel())->info('Payment created successfully', [
            'event' => 'payment.creation.success',
            'booking_id' => $booking->id,
            'transaction_id' => $transaction->id,
            'order_id' => $transaction->order_id,
            'amount' => $transaction->gross_amount,
            'expiry_time' => $transaction->expiry_time?->toIso8601String(),
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    /**
     * Log payment creation failure
     * 
     * Requirements: 11.1 - Log all Midtrans API failures with full context
     *
     * @param Booking $booking
     * @param string $orderId
     * @param \Throwable $exception
     * @return void
     */
    public static function logPaymentCreationFailure(Booking $booking, string $orderId, \Throwable $exception): void
    {
        Log::channel(self::getChannel())->error('Payment creation failed', [
            'event' => 'payment.creation.failed',
            'booking_id' => $booking->id,
            'order_id' => $orderId,
            'amount' => $booking->total_amount,
            'user_id' => $booking->user_id,
            'error_message' => $exception->getMessage(),
            'error_class' => get_class($exception),
            'error_code' => $exception->getCode(),
            'trace' => $exception->getTraceAsString(),
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    /**
     * Log Midtrans API call
     *
     * @param string $endpoint
     * @param array $params
     * @param string $environment
     * @return void
     */
    public static function logMidtransApiCall(string $endpoint, array $params, string $environment): void
    {
        Log::channel(self::getChannel())->info('Midtrans API call initiated', [
            'event' => 'midtrans.api.call',
            'endpoint' => $endpoint,
            'order_id' => $params['transaction_details']['order_id'] ?? null,
            'gross_amount' => $params['transaction_details']['gross_amount'] ?? null,
            'environment' => $environment,
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    /**
     * Log Midtrans API success
     *
     * @param string $endpoint
     * @param string|null $orderId
     * @param string $environment
     * @return void
     */
    public static function logMidtransApiSuccess(string $endpoint, ?string $orderId, string $environment): void
    {
        Log::channel(self::getChannel())->info('Midtrans API call successful', [
            'event' => 'midtrans.api.success',
            'endpoint' => $endpoint,
            'order_id' => $orderId,
            'environment' => $environment,
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    /**
     * Log Midtrans API failure
     * 
     * Requirements: 11.1 - Log all Midtrans API failures with full context
     *
     * @param string $endpoint
     * @param string|null $orderId
     * @param \Throwable $exception
     * @param string $environment
     * @param int|null $httpStatusCode
     * @return void
     */
    public static function logMidtransApiFailure(
        string $endpoint,
        ?string $orderId,
        \Throwable $exception,
        string $environment,
        ?int $httpStatusCode = null
    ): void {
        Log::channel(self::getChannel())->error('Midtrans API call failed', [
            'event' => 'midtrans.api.failed',
            'endpoint' => $endpoint,
            'order_id' => $orderId,
            'environment' => $environment,
            'http_status_code' => $httpStatusCode,
            'error_message' => $exception->getMessage(),
            'error_class' => get_class($exception),
            'trace' => $exception->getTraceAsString(),
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    /**
     * Log notification received
     *
     * @param array $notification
     * @return void
     */
    public static function logNotificationReceived(array $notification): void
    {
        Log::channel(self::getChannel())->info('Payment notification received', [
            'event' => 'notification.received',
            'order_id' => $notification['order_id'] ?? 'unknown',
            'transaction_status' => $notification['transaction_status'] ?? 'unknown',
            'payment_type' => $notification['payment_type'] ?? null,
            'gross_amount' => $notification['gross_amount'] ?? null,
            'ip_address' => request()->ip(),
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    /**
     * Log notification processing success
     *
     * @param string $orderId
     * @param string $oldStatus
     * @param string $newStatus
     * @return void
     */
    public static function logNotificationProcessed(string $orderId, string $oldStatus, string $newStatus): void
    {
        Log::channel(self::getChannel())->info('Payment notification processed', [
            'event' => 'notification.processed',
            'order_id' => $orderId,
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    /**
     * Log notification processing failure
     * 
     * Requirements: 11.2 - Log notification processing failures
     *
     * @param array $notification
     * @param \Throwable $exception
     * @return void
     */
    public static function logNotificationProcessingFailure(array $notification, \Throwable $exception): void
    {
        Log::channel(self::getChannel())->error('Payment notification processing failed', [
            'event' => 'notification.processing.failed',
            'order_id' => $notification['order_id'] ?? 'unknown',
            'transaction_status' => $notification['transaction_status'] ?? 'unknown',
            'ip_address' => request()->ip(),
            'error_message' => $exception->getMessage(),
            'error_class' => get_class($exception),
            'notification_data' => self::sanitizeNotificationForLog($notification),
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    /**
     * Log signature verification failure
     * 
     * Requirements: 11.3 - Log signature verification failures with IP address
     *
     * @param array $notification
     * @return void
     */
    public static function logSignatureVerificationFailure(array $notification): void
    {
        Log::channel(self::getChannel())->warning('Signature verification failed', [
            'event' => 'signature.verification.failed',
            'order_id' => $notification['order_id'] ?? 'unknown',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'notification_data' => self::sanitizeNotificationForLog($notification),
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    /**
     * Log payment status change
     * 
     * Requirements: 11.4 - Log all payment status changes with timestamp
     *
     * @param Transaction $transaction
     * @param string $oldStatus
     * @param string $newStatus
     * @param string|null $trigger Source of the status change
     * @return void
     */
    public static function logPaymentStatusChange(
        Transaction $transaction,
        string $oldStatus,
        string $newStatus,
        ?string $trigger = null
    ): void {
        Log::channel(self::getChannel())->info('Payment status changed', [
            'event' => 'payment.status.changed',
            'transaction_id' => $transaction->id,
            'order_id' => $transaction->order_id,
            'booking_id' => $transaction->booking_id,
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
            'trigger' => $trigger ?? 'unknown',
            'payment_type' => $transaction->payment_type,
            'amount' => $transaction->gross_amount,
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    /**
     * Log booking status change
     *
     * @param Booking $booking
     * @param string $oldStatus
     * @param string $newStatus
     * @param Transaction|null $transaction
     * @return void
     */
    public static function logBookingStatusChange(
        Booking $booking,
        string $oldStatus,
        string $newStatus,
        ?Transaction $transaction = null
    ): void {
        Log::channel(self::getChannel())->info('Booking status changed', [
            'event' => 'booking.status.changed',
            'booking_id' => $booking->id,
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
            'transaction_id' => $transaction?->id,
            'order_id' => $transaction?->order_id,
            'payment_date' => $booking->payment_date?->toIso8601String(),
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    /**
     * Log payment retry attempt
     *
     * @param Booking $booking
     * @param int $retryCount
     * @return void
     */
    public static function logPaymentRetryAttempt(Booking $booking, int $retryCount): void
    {
        Log::channel(self::getChannel())->info('Payment retry initiated', [
            'event' => 'payment.retry.attempt',
            'booking_id' => $booking->id,
            'retry_count' => $retryCount,
            'user_id' => $booking->user_id,
            'amount' => $booking->total_amount,
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    /**
     * Log transaction expiration
     *
     * @param Transaction $transaction
     * @return void
     */
    public static function logTransactionExpired(Transaction $transaction): void
    {
        Log::channel(self::getChannel())->info('Transaction expired', [
            'event' => 'transaction.expired',
            'transaction_id' => $transaction->id,
            'order_id' => $transaction->order_id,
            'booking_id' => $transaction->booking_id,
            'expiry_time' => $transaction->expiry_time?->toIso8601String(),
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    /**
     * Log manual payment status check attempt
     * 
     * Requirements: 7.4 - Log manual check attempts
     *
     * @param string $orderId
     * @param int|null $adminId
     * @return void
     */
    public static function logManualStatusCheckAttempt(string $orderId, ?int $adminId = null): void
    {
        Log::channel(self::getChannel())->info('Manual payment status check initiated', [
            'event' => 'payment.manual_check.attempt',
            'order_id' => $orderId,
            'admin_id' => $adminId ?? (Auth::check() ? Auth::id() : null),
            'ip_address' => request()->ip(),
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    /**
     * Log manual payment status check success
     * 
     * Requirements: 7.4 - Log manual check attempts
     *
     * @param string $orderId
     * @param array $midtransStatus
     * @param string|null $oldStatus
     * @param string|null $newStatus
     * @return void
     */
    public static function logManualStatusCheckSuccess(
        string $orderId,
        array $midtransStatus,
        ?string $oldStatus = null,
        ?string $newStatus = null
    ): void {
        Log::channel(self::getChannel())->info('Manual payment status check completed', [
            'event' => 'payment.manual_check.success',
            'order_id' => $orderId,
            'midtrans_status' => $midtransStatus['transaction_status'] ?? 'unknown',
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
            'status_changed' => $oldStatus !== $newStatus,
            'admin_id' => Auth::check() ? Auth::id() : null,
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    /**
     * Log manual payment status check failure
     * 
     * Requirements: 7.4 - Log manual check attempts
     *
     * @param string $orderId
     * @param \Throwable $exception
     * @return void
     */
    public static function logManualStatusCheckFailure(string $orderId, \Throwable $exception): void
    {
        Log::channel(self::getChannel())->error('Manual payment status check failed', [
            'event' => 'payment.manual_check.failed',
            'order_id' => $orderId,
            'admin_id' => Auth::check() ? Auth::id() : null,
            'error_message' => $exception->getMessage(),
            'error_class' => get_class($exception),
            'ip_address' => request()->ip(),
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    /**
     * Log amount mismatch (potential fraud)
     *
     * @param string $orderId
     * @param string $expectedAmount
     * @param string $receivedAmount
     * @param array $notification
     * @return void
     */
    public static function logAmountMismatch(
        string $orderId,
        string $expectedAmount,
        string $receivedAmount,
        array $notification
    ): void {
        Log::channel(self::getChannel())->critical('POTENTIAL FRAUD: Amount mismatch detected', [
            'event' => 'security.amount_mismatch',
            'order_id' => $orderId,
            'expected_amount' => $expectedAmount,
            'received_amount' => $receivedAmount,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'notification_data' => self::sanitizeNotificationForLog($notification),
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    /**
     * Log raw notification for debugging
     * 
     * Requirements: 11.5 - Store raw notification payload for debugging
     *
     * @param array $notification
     * @param Transaction $transaction
     * @return void
     */
    public static function logRawNotification(array $notification, Transaction $transaction): void
    {
        Log::channel(self::getChannel())->debug('Raw notification stored', [
            'event' => 'notification.raw_stored',
            'order_id' => $transaction->order_id,
            'transaction_id' => $transaction->id,
            'raw_notification' => self::sanitizeNotificationForLog($notification),
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    /**
     * Sanitize notification data for logging
     *
     * @param array $notification
     * @return array
     */
    protected static function sanitizeNotificationForLog(array $notification): array
    {
        $sanitized = $notification;
        
        // Mask signature key for security
        if (isset($sanitized['signature_key'])) {
            $sanitized['signature_key'] = substr($sanitized['signature_key'], 0, 20) . '...';
        }

        return $sanitized;
    }

    /**
     * Get the log channel to use
     * 
     * Falls back to default channel if payment channel is not configured
     *
     * @return string
     */
    protected static function getChannel(): string
    {
        // Check if payment channel exists in config
        $channels = config('logging.channels', []);
        
        if (isset($channels[self::CHANNEL])) {
            return self::CHANNEL;
        }

        // Fall back to default channel
        return config('logging.default', 'stack');
    }
}
