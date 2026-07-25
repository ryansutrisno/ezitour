<?php

namespace App\Services;

use App\Exceptions\MidtransApiException;
use App\Exceptions\NotificationProcessingException;
use App\Exceptions\PaymentException;
use App\Models\Booking;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * PaymentService - Core business logic untuk payment processing
 *
 * Class ini menangani:
 * - Create payment transactions
 * - Generate Snap Tokens dari Midtrans
 * - Process payment notifications
 * - Update transaction dan booking status
 * - Handle payment retry logic
 *
 * Requirements: 11.1, 11.2, 11.3, 11.4 - Comprehensive error handling and logging
 */
class PaymentService
{
    /**
     * MidtransClient instance
     */
    protected MidtransClient $midtransClient;

    /**
     * Create a new PaymentService instance.
     */
    public function __construct(MidtransClient $midtransClient)
    {
        $this->midtransClient = $midtransClient;
    }

    /**
     * Generate unique Order ID untuk transaksi
     *
     * Format: BOOK-{booking_id}-{timestamp}-{random}
     * Contoh: BOOK-123-1703750400-a8f3
     */
    public function generateOrderId(Booking $booking): string
    {
        $timestamp = now()->timestamp;
        $random = Str::lower(Str::random(4));

        return sprintf('BOOK-%d-%d-%s', $booking->id, $timestamp, $random);
    }

    /**
     * Create a new payment transaction and get Snap Token
     *
     * @return array ['snap_token' => string, 'transaction' => Transaction]
     *
     * @throws PaymentException
     */
    public function createPayment(Booking $booking): array
    {
        // Validate booking can be paid
        $this->validateBookingForPayment($booking);

        // Generate unique order ID early for logging
        $orderId = $this->generateOrderId($booking);

        // Log payment creation attempt
        PaymentLogger::logPaymentCreationAttempt($booking, $orderId);

        return DB::transaction(function () use ($booking, $orderId) {
            // Build Snap parameters
            $snapParams = $this->buildSnapParams($booking, $orderId);

            try {
                // Request Snap Token from Midtrans
                $snapToken = $this->midtransClient->getSnapToken($snapParams);

                // Create transaction record
                $transaction = $this->createTransactionRecord($booking, $orderId, $snapToken);

                // Log successful payment creation
                PaymentLogger::logPaymentCreationSuccess($booking, $transaction);

                return [
                    'snap_token' => $snapToken,
                    'transaction' => $transaction,
                ];

            } catch (MidtransApiException $e) {
                // Log failure with full context
                PaymentLogger::logPaymentCreationFailure($booking, $orderId, $e);

                // Re-throw with user-friendly message
                throw new PaymentException(
                    $e->getMessage(),
                    $e->getUserMessage(),
                    [
                        'booking_id' => $booking->id,
                        'order_id' => $orderId,
                    ],
                    500,
                    $e
                );

            } catch (\Exception $e) {
                // Log unexpected failure
                PaymentLogger::logPaymentCreationFailure($booking, $orderId, $e);

                throw new PaymentException(
                    "Unexpected error creating payment: {$e->getMessage()}",
                    'Gagal membuat pembayaran. Silakan coba lagi nanti.',
                    [
                        'booking_id' => $booking->id,
                        'order_id' => $orderId,
                    ],
                    500,
                    $e
                );
            }
        });
    }

    /**
     * Build Snap parameters untuk Midtrans API request
     */
    protected function buildSnapParams(Booking $booking, string $orderId): array
    {
        // Load relationships if not loaded
        $booking->loadMissing(['user', 'package']);

        $grossAmount = (int) $booking->total_amount;

        return [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => $grossAmount,
            ],
            'customer_details' => $this->buildCustomerDetails($booking),
            'item_details' => $this->buildItemDetails($booking, $grossAmount),
            'callbacks' => [
                'finish' => route('payments.finish'),
                'unfinish' => route('payments.unfinish'),
                'error' => route('payments.error'),
            ],
        ];
    }

    /**
     * Build customer details untuk Snap parameters
     */
    protected function buildCustomerDetails(Booking $booking): array
    {
        $user = $booking->user;

        return [
            'first_name' => $user->name ?? 'Customer',
            'email' => $user->email ?? '',
            'phone' => $user->phone ?? '',
        ];
    }

    /**
     * Build item details untuk Snap parameters
     */
    protected function buildItemDetails(Booking $booking, int $grossAmount): array
    {
        $package = $booking->package;
        $packageName = $package ? $package->name : 'Travel Package';

        return [
            [
                'id' => 'PKG-'.($package->id ?? $booking->package_id),
                'price' => $grossAmount,
                'quantity' => 1,
                'name' => Str::limit($packageName, 50),
            ],
        ];
    }

    /**
     * Create transaction record in database
     */
    protected function createTransactionRecord(Booking $booking, string $orderId, string $snapToken): Transaction
    {
        // Calculate expiry time based on configuration (Requirements 9.1)
        $expiryMinutes = (int) config('midtrans.expiry_duration', Transaction::DEFAULT_EXPIRY_MINUTES);
        $expiryTime = now()->addMinutes($expiryMinutes);

        return Transaction::create([
            'booking_id' => $booking->id,
            'order_id' => $orderId,
            'snap_token' => $snapToken,
            'gross_amount' => $booking->total_amount,
            'transaction_status' => Transaction::STATUS_PENDING,
            'transaction_time' => now(),
            'expiry_time' => $expiryTime,
        ]);
    }

    /**
     * Validate booking can be paid
     *
     * @throws \Exception
     */
    protected function validateBookingForPayment(Booking $booking): void
    {
        // Check if booking is already paid
        if ($booking->isPaid()) {
            throw new \Exception('Booking ini sudah dibayar.');
        }

        // Check if there's a pending transaction
        if ($booking->hasPendingPayment()) {
            throw new \Exception('Booking ini memiliki pembayaran yang sedang diproses.');
        }

        // Validate amount is positive
        if ($booking->total_amount <= 0) {
            throw new \Exception('Total pembayaran tidak valid.');
        }
    }

    /**
     * Get MidtransClient instance
     */
    public function getMidtransClient(): MidtransClient
    {
        return $this->midtransClient;
    }

    /**
     * Process payment notification from Midtrans webhook
     *
     * @param  array  $notification  Raw notification data from Midtrans
     *
     * @throws \App\Exceptions\InvalidSignatureException
     * @throws NotificationProcessingException
     */
    public function processNotification(array $notification): void
    {
        // Log notification received
        PaymentLogger::logNotificationReceived($notification);

        // Step 1: Verify notification signature
        if (! $this->midtransClient->verifySignature($notification)) {
            // Signature verification failure is already logged by MidtransClient
            throw new \App\Exceptions\InvalidSignatureException(
                'Invalid notification signature'
            );
        }

        // Step 2: Extract order_id and transaction_status
        $orderId = $notification['order_id'] ?? null;
        $midtransStatus = $notification['transaction_status'] ?? null;
        $fraudStatus = $notification['fraud_status'] ?? null;

        if (empty($orderId)) {
            throw NotificationProcessingException::missingOrderId($notification);
        }

        // Find the transaction
        $transaction = Transaction::where('order_id', $orderId)->first();

        if (! $transaction) {
            throw NotificationProcessingException::transactionNotFound($orderId, $notification);
        }

        // Validate amount matches (security check)
        $notificationAmount = $notification['gross_amount'] ?? null;
        if ($notificationAmount !== null) {
            $expectedAmount = number_format((float) $transaction->gross_amount, 2, '.', '');
            $receivedAmount = number_format((float) $notificationAmount, 2, '.', '');

            if ($expectedAmount !== $receivedAmount) {
                // Log amount mismatch as potential fraud
                PaymentLogger::logAmountMismatch($orderId, $expectedAmount, $receivedAmount, $notification);

                throw NotificationProcessingException::amountMismatch(
                    $orderId,
                    $expectedAmount,
                    $receivedAmount,
                    $notification
                );
            }
        }

        // Step 3: Map Midtrans status to internal status
        $oldStatus = $transaction->transaction_status;
        $internalStatus = $this->mapMidtransStatus($midtransStatus, $fraudStatus);

        // Step 4 & 6: Update Transaction status and store raw notification
        try {
            DB::transaction(function () use ($transaction, $notification, $internalStatus, $oldStatus) {
                // Update transaction with notification data
                $transaction->update([
                    'transaction_status' => $internalStatus,
                    'payment_type' => $notification['payment_type'] ?? $transaction->payment_type,
                    'status_code' => $notification['status_code'] ?? $transaction->status_code,
                    'fraud_status' => $notification['fraud_status'] ?? $transaction->fraud_status,
                    'settlement_time' => $this->parseSettlementTime($notification),
                    'raw_notification' => $notification, // Store raw notification data (Requirement 11.5)
                ]);

                // Log status change
                PaymentLogger::logPaymentStatusChange(
                    $transaction->fresh(),
                    $oldStatus,
                    $internalStatus,
                    'midtrans_notification'
                );

                // Log raw notification stored
                PaymentLogger::logRawNotification($notification, $transaction);

                // Log notification processed
                PaymentLogger::logNotificationProcessed($transaction->order_id, $oldStatus, $internalStatus);

                // Step 5: Update Booking status based on transaction status
                $this->updateBookingStatus($transaction->fresh());
            });

        } catch (\Exception $e) {
            throw NotificationProcessingException::databaseError(
                $orderId,
                $e->getMessage(),
                $notification,
                $e
            );
        }
    }

    /**
     * Map Midtrans transaction status to internal status
     *
     * Mapping rules:
     * - capture (with fraud_status accept) -> paid
     * - settlement -> paid
     * - pending -> pending
     * - deny, cancel, expire -> failed
     */
    protected function mapMidtransStatus(?string $midtransStatus, ?string $fraudStatus = null): string
    {
        // Handle capture status with fraud check
        if ($midtransStatus === 'capture') {
            // For credit card transactions, check fraud status
            if ($fraudStatus === 'accept') {
                return Transaction::STATUS_PAID;
            } elseif ($fraudStatus === 'challenge') {
                // Challenge status should be reviewed manually
                return Transaction::STATUS_PENDING;
            } else {
                // Deny or unknown fraud status
                return Transaction::STATUS_FAILED;
            }
        }

        // Map other statuses
        return match ($midtransStatus) {
            'settlement' => Transaction::STATUS_PAID,
            'pending' => Transaction::STATUS_PENDING,
            'deny', 'cancel' => Transaction::STATUS_FAILED,
            'expire' => Transaction::STATUS_EXPIRED,
            default => Transaction::STATUS_PENDING,
        };
    }

    /**
     * Update booking status based on transaction status
     *
     * This method implements the status synchronization logic:
     * - Property 9: Cascading Status Update (paid transaction -> paid booking)
     * - Property 10: Failed Payment Isolation (failed transaction -> booking stays pending)
     * - Property 11: Latest Transaction Priority (only latest transaction updates booking)
     * - Property 12: Status Immutability (paid booking cannot be downgraded)
     *
     * @return bool True if booking was updated, false otherwise
     */
    public function updateBookingStatus(Transaction $transaction): bool
    {
        $booking = $transaction->booking;

        if (! $booking) {
            Log::error('Booking not found for transaction', [
                'transaction_id' => $transaction->id,
                'order_id' => $transaction->order_id,
            ]);

            return false;
        }

        $oldStatus = $booking->status;

        // Property 12: Status Immutability - Prevent status downgrade from paid to pending
        if ($booking->isPaid()) {
            Log::debug('Booking already paid, status immutable - skipping update', [
                'booking_id' => $booking->id,
                'order_id' => $transaction->order_id,
                'transaction_status' => $transaction->transaction_status,
            ]);

            return false;
        }

        // Property 11: Latest Transaction Priority - Only update if this is the latest transaction
        $latestTransaction = $booking->latestTransaction;
        if ($latestTransaction && $latestTransaction->id !== $transaction->id) {
            Log::debug('Not the latest transaction, skipping booking update', [
                'booking_id' => $booking->id,
                'current_transaction_id' => $transaction->id,
                'latest_transaction_id' => $latestTransaction->id,
            ]);

            return false;
        }

        // Property 10: Failed Payment Isolation - Failed transactions don't change booking status
        if ($transaction->isFailed() || $transaction->isExpired()) {
            Log::debug('Transaction failed/expired, booking status remains pending', [
                'booking_id' => $booking->id,
                'order_id' => $transaction->order_id,
                'transaction_status' => $transaction->transaction_status,
            ]);

            return false;
        }

        // Property 9: Cascading Status Update - Only paid transactions update booking to paid
        if ($transaction->isPaid()) {
            $booking->update([
                'status' => 'paid',
                'payment_date' => now(),
            ]);

            // Log booking status change
            PaymentLogger::logBookingStatusChange($booking->fresh(), $oldStatus, 'paid', $transaction);

            return true;
        }

        // Pending transactions don't change booking status
        Log::debug('Transaction pending, no booking status change', [
            'booking_id' => $booking->id,
            'order_id' => $transaction->order_id,
            'transaction_status' => $transaction->transaction_status,
        ]);

        return false;
    }

    /**
     * Update booking status to paid (legacy method, calls updateBookingStatus)
     *
     * @deprecated Use updateBookingStatus() instead
     */
    protected function updateBookingToPaid(Transaction $transaction): void
    {
        $this->updateBookingStatus($transaction);
    }

    /**
     * Parse settlement time from notification
     */
    protected function parseSettlementTime(array $notification): ?\Carbon\Carbon
    {
        $settlementTime = $notification['settlement_time'] ?? null;

        if (empty($settlementTime)) {
            return null;
        }

        try {
            return \Carbon\Carbon::parse($settlementTime);
        } catch (\Exception $e) {
            Log::warning('Failed to parse settlement_time', [
                'settlement_time' => $settlementTime,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Retry payment for a booking with failed or expired transaction
     *
     * This method implements the payment retry mechanism:
     * - Property 14: Payment Retry Creates New Transaction
     *   - Check if booking has failed/expired transaction
     *   - Mark previous transaction as superseded
     *   - Create new transaction with new Snap Token
     *   - Use the same booking_id
     *
     * @return array ['snap_token' => string, 'transaction' => Transaction]
     *
     * @throws PaymentException
     */
    public function retryPayment(Booking $booking): array
    {
        // Validate booking can retry payment
        $this->validateBookingForRetry($booking);

        // Get retry count for logging
        $retryCount = $booking->transactions()->count();
        PaymentLogger::logPaymentRetryAttempt($booking, $retryCount);

        return DB::transaction(function () use ($booking, $retryCount) {
            // Mark all previous failed/expired transactions as superseded
            $this->markPreviousTransactionsAsSuperseded($booking);

            // Generate new unique order ID
            $orderId = $this->generateOrderId($booking);

            // Build Snap parameters
            $snapParams = $this->buildSnapParams($booking, $orderId);

            try {
                // Request new Snap Token from Midtrans
                $snapToken = $this->midtransClient->getSnapToken($snapParams);

                // Create new transaction record
                $transaction = $this->createTransactionRecord($booking, $orderId, $snapToken);

                // Log successful retry
                PaymentLogger::logPaymentCreationSuccess($booking, $transaction);

                Log::info('Payment retry created successfully', [
                    'booking_id' => $booking->id,
                    'order_id' => $orderId,
                    'amount' => $booking->total_amount,
                    'retry_count' => $retryCount + 1,
                ]);

                return [
                    'snap_token' => $snapToken,
                    'transaction' => $transaction,
                ];

            } catch (MidtransApiException $e) {
                PaymentLogger::logPaymentCreationFailure($booking, $orderId, $e);

                throw new PaymentException(
                    $e->getMessage(),
                    $e->getUserMessage(),
                    [
                        'booking_id' => $booking->id,
                        'order_id' => $orderId,
                        'retry_count' => $retryCount,
                    ],
                    500,
                    $e
                );

            } catch (\Exception $e) {
                PaymentLogger::logPaymentCreationFailure($booking, $orderId, $e);

                throw new PaymentException(
                    "Unexpected error during payment retry: {$e->getMessage()}",
                    'Gagal mengulang pembayaran. Silakan coba lagi nanti.',
                    [
                        'booking_id' => $booking->id,
                        'order_id' => $orderId,
                        'retry_count' => $retryCount,
                    ],
                    500,
                    $e
                );
            }
        });
    }

    /**
     * Validate booking can retry payment
     *
     * @throws \Exception
     */
    protected function validateBookingForRetry(Booking $booking): void
    {
        // Check if booking is already paid
        if ($booking->isPaid()) {
            throw new \Exception('Booking ini sudah dibayar.');
        }

        // Check if there's a pending transaction (cannot retry while pending)
        if ($booking->hasPendingPayment()) {
            throw new \Exception('Booking ini memiliki pembayaran yang sedang diproses. Silakan tunggu atau batalkan pembayaran sebelumnya.');
        }

        // Check if booking has any failed or expired transaction to retry
        $hasRetryableTransaction = $booking->transactions()
            ->whereIn('transaction_status', [
                Transaction::STATUS_FAILED,
                Transaction::STATUS_EXPIRED,
            ])
            ->exists();

        if (! $hasRetryableTransaction) {
            // If no transactions exist at all, this should use createPayment instead
            $hasAnyTransaction = $booking->transactions()->exists();

            if (! $hasAnyTransaction) {
                throw new \Exception('Tidak ada transaksi sebelumnya. Gunakan pembayaran baru.');
            }

            throw new \Exception('Tidak ada transaksi yang gagal atau kadaluarsa untuk diulang.');
        }

        // Validate amount is positive
        if ($booking->total_amount <= 0) {
            throw new \Exception('Total pembayaran tidak valid.');
        }
    }

    /**
     * Mark all previous failed/expired transactions as superseded
     *
     * @return int Number of transactions marked as superseded
     */
    protected function markPreviousTransactionsAsSuperseded(Booking $booking): int
    {
        $affectedRows = $booking->transactions()
            ->whereIn('transaction_status', [
                Transaction::STATUS_FAILED,
                Transaction::STATUS_EXPIRED,
            ])
            ->update([
                'transaction_status' => Transaction::STATUS_SUPERSEDED,
            ]);

        if ($affectedRows > 0) {
            Log::info('Previous transactions marked as superseded', [
                'booking_id' => $booking->id,
                'affected_count' => $affectedRows,
            ]);
        }

        return $affectedRows;
    }

    /**
     * Check if a booking can retry payment
     */
    public function canRetryPayment(Booking $booking): bool
    {
        // Cannot retry if already paid
        if ($booking->isPaid()) {
            return false;
        }

        // Cannot retry if there's a pending transaction
        if ($booking->hasPendingPayment()) {
            return false;
        }

        // Can retry if there's a failed or expired transaction
        return $booking->transactions()
            ->whereIn('transaction_status', [
                Transaction::STATUS_FAILED,
                Transaction::STATUS_EXPIRED,
            ])
            ->exists();
    }

    /**
     * Check and update expired transactions
     *
     * This method scans for pending transactions that have passed their expiry_time
     * and updates their status to 'expired'.
     *
     * Requirements: 9.2 - WHEN payment expires THEN THE Payment_System SHALL update
     * Transaction status to "expired"
     *
     * @return int Number of transactions marked as expired
     */
    public function checkAndUpdateExpiredTransactions(): int
    {
        $expiredTransactions = Transaction::where('transaction_status', Transaction::STATUS_PENDING)
            ->whereNotNull('expiry_time')
            ->where('expiry_time', '<', now())
            ->get();

        $count = 0;

        foreach ($expiredTransactions as $transaction) {
            $oldStatus = $transaction->transaction_status;

            $transaction->update([
                'transaction_status' => Transaction::STATUS_EXPIRED,
            ]);

            // Log transaction expiration
            PaymentLogger::logTransactionExpired($transaction);
            PaymentLogger::logPaymentStatusChange($transaction->fresh(), $oldStatus, Transaction::STATUS_EXPIRED, 'expiry_check');

            $count++;
        }

        if ($count > 0) {
            Log::info('Expired transactions batch update completed', [
                'total_expired' => $count,
            ]);
        }

        return $count;
    }

    /**
     * Check if a specific transaction has expired and update its status
     *
     * Requirements: 9.2 - WHEN payment expires THEN THE Payment_System SHALL update
     * Transaction status to "expired"
     *
     * @return bool True if transaction was expired, false otherwise
     */
    public function checkAndExpireTransaction(Transaction $transaction): bool
    {
        // Only check pending transactions
        if (! $transaction->isPending()) {
            return false;
        }

        // Check if transaction has expired
        if (! $transaction->hasExpired()) {
            return false;
        }

        $oldStatus = $transaction->transaction_status;

        // Update status to expired
        $transaction->update([
            'transaction_status' => Transaction::STATUS_EXPIRED,
        ]);

        // Log transaction expiration
        PaymentLogger::logTransactionExpired($transaction);
        PaymentLogger::logPaymentStatusChange($transaction->fresh(), $oldStatus, Transaction::STATUS_EXPIRED, 'expiry_check');

        return true;
    }

    /**
     * Get time remaining for a pending transaction
     *
     * Requirements: 9.4 - THE Payment_System SHALL display time remaining for pending payments
     *
     * @return array|null ['minutes' => int, 'formatted' => string] or null if not applicable
     */
    public function getTransactionTimeRemaining(Transaction $transaction): ?array
    {
        // Only applicable for pending transactions
        if (! $transaction->isPending()) {
            return null;
        }

        $minutes = $transaction->getTimeRemainingMinutes();

        if ($minutes === null) {
            return null;
        }

        return [
            'minutes' => $minutes,
            'formatted' => $transaction->getTimeRemainingFormatted(),
            'expiry_time' => $transaction->expiry_time?->toDateTimeString(),
        ];
    }

    /**
     * Check if booking has any expired transactions that can be retried
     *
     * Requirements: 9.3 - WHEN Transaction expires THEN THE Payment_System SHALL
     * allow customer to retry payment
     */
    public function hasExpiredTransactionForRetry(Booking $booking): bool
    {
        // Cannot retry if already paid
        if ($booking->isPaid()) {
            return false;
        }

        // Cannot retry if there's a pending transaction
        if ($booking->hasPendingPayment()) {
            return false;
        }

        // Check for expired transactions
        return $booking->transactions()
            ->where('transaction_status', Transaction::STATUS_EXPIRED)
            ->exists();
    }

    /**
     * Check payment status from Midtrans API
     *
     * This method calls Midtrans API to get the current status of a transaction
     * and updates the local transaction record if needed.
     *
     * Requirements: 7.4 - THE Payment_System SHALL allow admin to manually check
     * payment status from Midtrans API
     *
     * @return array Status information from Midtrans
     *
     * @throws MidtransApiException
     */
    public function checkPaymentStatus(string $orderId): array
    {
        // Log manual status check attempt
        PaymentLogger::logManualStatusCheckAttempt($orderId);

        // Find the transaction
        $transaction = Transaction::where('order_id', $orderId)->first();

        if (! $transaction) {
            $exception = new \Exception("Transaction not found: {$orderId}");
            PaymentLogger::logManualStatusCheckFailure($orderId, $exception);
            throw $exception;
        }

        $oldStatus = $transaction->transaction_status;

        try {
            // Get status from Midtrans API
            $status = $this->midtransClient->getTransactionStatus($orderId);

            // If status has changed, update the transaction
            $midtransStatus = $status['transaction_status'] ?? null;
            $fraudStatus = $status['fraud_status'] ?? null;
            $newStatus = $oldStatus;

            if ($midtransStatus) {
                $newStatus = $this->mapMidtransStatus($midtransStatus, $fraudStatus);

                // Only update if status has changed
                if ($oldStatus !== $newStatus) {
                    $transaction->update([
                        'transaction_status' => $newStatus,
                        'payment_type' => $status['payment_type'] ?? $transaction->payment_type,
                        'status_code' => $status['status_code'] ?? $transaction->status_code,
                        'fraud_status' => $fraudStatus ?? $transaction->fraud_status,
                        'settlement_time' => isset($status['settlement_time'])
                            ? \Carbon\Carbon::parse($status['settlement_time'])
                            : $transaction->settlement_time,
                    ]);

                    // Log status change
                    PaymentLogger::logPaymentStatusChange(
                        $transaction->fresh(),
                        $oldStatus,
                        $newStatus,
                        'manual_check'
                    );

                    // Update booking status if needed
                    $this->updateBookingStatus($transaction->fresh());
                }
            }

            // Log successful manual check
            PaymentLogger::logManualStatusCheckSuccess($orderId, $status, $oldStatus, $newStatus);

            return $status;

        } catch (MidtransApiException $e) {
            PaymentLogger::logManualStatusCheckFailure($orderId, $e);
            throw $e;
        } catch (\Exception $e) {
            PaymentLogger::logManualStatusCheckFailure($orderId, $e);
            throw $e;
        }
    }
}
