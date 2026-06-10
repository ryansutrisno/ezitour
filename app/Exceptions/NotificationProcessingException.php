<?php

namespace App\Exceptions;

use Illuminate\Support\Facades\Log;

/**
 * Exception thrown when notification processing fails.
 * 
 * This exception handles errors during Midtrans webhook notification processing
 * without breaking the webhook response.
 * 
 * Requirements: 11.2 - Log notification processing failures without breaking webhook
 */
class NotificationProcessingException extends PaymentException
{
    /**
     * Order ID from notification
     */
    protected ?string $orderId = null;

    /**
     * Raw notification data
     */
    protected array $notificationData = [];

    /**
     * Create a new NotificationProcessingException instance.
     *
     * @param string $message Technical error message
     * @param string $userMessage User-friendly message
     * @param string|null $orderId Order ID from notification
     * @param array $notificationData Raw notification data
     * @param array $context Additional context
     * @param \Throwable|null $previous Previous exception
     */
    public function __construct(
        string $message,
        string $userMessage = 'Gagal memproses notifikasi pembayaran.',
        ?string $orderId = null,
        array $notificationData = [],
        array $context = [],
        ?\Throwable $previous = null
    ) {
        $this->orderId = $orderId;
        $this->notificationData = $notificationData;

        // Enrich context with notification-specific data
        $enrichedContext = array_merge($context, [
            'order_id' => $orderId,
            'notification_data' => $this->sanitizeNotificationData($notificationData),
            'ip_address' => request()->ip(),
        ]);

        parent::__construct($message, $userMessage, $enrichedContext, 500, $previous);
    }

    /**
     * Create exception for missing order ID
     *
     * @param array $notificationData
     * @return static
     */
    public static function missingOrderId(array $notificationData): static
    {
        return new static(
            'Missing order_id in notification',
            'Data notifikasi tidak lengkap.',
            null,
            $notificationData
        );
    }

    /**
     * Create exception for transaction not found
     *
     * @param string $orderId
     * @param array $notificationData
     * @return static
     */
    public static function transactionNotFound(string $orderId, array $notificationData = []): static
    {
        return new static(
            "Transaction not found for order_id: {$orderId}",
            'Transaksi tidak ditemukan.',
            $orderId,
            $notificationData
        );
    }

    /**
     * Create exception for amount mismatch (potential fraud)
     *
     * @param string $orderId
     * @param string $expectedAmount
     * @param string $receivedAmount
     * @param array $notificationData
     * @return static
     */
    public static function amountMismatch(string $orderId, string $expectedAmount, string $receivedAmount, array $notificationData = []): static
    {
        Log::critical('POTENTIAL FRAUD: Amount mismatch detected', [
            'order_id' => $orderId,
            'expected_amount' => $expectedAmount,
            'received_amount' => $receivedAmount,
            'ip_address' => request()->ip(),
            'timestamp' => now()->toIso8601String(),
        ]);

        return new static(
            "Amount mismatch for order {$orderId}: expected {$expectedAmount}, received {$receivedAmount}",
            'Jumlah pembayaran tidak sesuai.',
            $orderId,
            $notificationData,
            [
                'expected_amount' => $expectedAmount,
                'received_amount' => $receivedAmount,
                'fraud_indicator' => true,
            ]
        );
    }

    /**
     * Create exception for database update failure
     *
     * @param string $orderId
     * @param string $errorMessage
     * @param array $notificationData
     * @param \Throwable|null $previous
     * @return static
     */
    public static function databaseError(string $orderId, string $errorMessage, array $notificationData = [], ?\Throwable $previous = null): static
    {
        return new static(
            "Database error while processing notification for order {$orderId}: {$errorMessage}",
            'Gagal menyimpan status pembayaran.',
            $orderId,
            $notificationData,
            [],
            $previous
        );
    }

    /**
     * Sanitize notification data for logging
     *
     * @param array $data
     * @return array
     */
    protected function sanitizeNotificationData(array $data): array
    {
        $sanitized = $data;
        
        // Mask signature key
        if (isset($sanitized['signature_key'])) {
            $sanitized['signature_key'] = substr($sanitized['signature_key'], 0, 20) . '...';
        }

        return $sanitized;
    }

    /**
     * Get order ID
     *
     * @return string|null
     */
    public function getOrderId(): ?string
    {
        return $this->orderId;
    }
}
