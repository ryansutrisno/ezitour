<?php

namespace App\Services;

use App\Exceptions\MidtransApiException;
use Illuminate\Support\Facades\Log;
use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Transaction;

/**
 * MidtransClient - Wrapper untuk Midtrans API
 *
 * Class ini menangani komunikasi HTTP dengan Midtrans API,
 * mengelola autentikasi dengan server key, dan memformat request/response data.
 *
 * Requirements: 11.1 - Comprehensive logging for all API failures
 */
class MidtransClient
{
    /**
     * Sandbox API base URL
     */
    protected const SANDBOX_BASE_URL = 'https://api.sandbox.midtrans.com';

    /**
     * Production API base URL
     */
    protected const PRODUCTION_BASE_URL = 'https://api.midtrans.com';

    /**
     * Server key untuk autentikasi
     */
    protected string $serverKey;

    /**
     * Client key untuk frontend
     */
    protected string $clientKey;

    /**
     * Flag untuk production mode
     */
    protected bool $isProduction;

    /**
     * Flag untuk sanitized mode
     */
    protected bool $isSanitized;

    /**
     * Flag untuk 3DS mode
     */
    protected bool $is3ds;

    /**
     * Initialize MidtransClient dengan konfigurasi dari config/midtrans.php
     */
    public function __construct()
    {
        $this->serverKey = config('midtrans.server_key', '');
        $this->clientKey = config('midtrans.client_key', '');
        $this->isProduction = config('midtrans.is_production', false);
        $this->isSanitized = config('midtrans.is_sanitized', true);
        $this->is3ds = config('midtrans.is_3ds', true);

        $this->initializeConfig();
    }

    /**
     * Initialize Midtrans SDK configuration
     */
    protected function initializeConfig(): void
    {
        Config::$serverKey = $this->serverKey;
        Config::$clientKey = $this->clientKey;
        Config::$isProduction = $this->isProduction;
        Config::$isSanitized = $this->isSanitized;
        Config::$is3ds = $this->is3ds;
    }

    /**
     * Request Snap Token dari Midtrans
     *
     * @param  array  $params  Parameter untuk Snap Token request
     *                         - transaction_details: ['order_id' => string, 'gross_amount' => int]
     *                         - customer_details: ['first_name' => string, 'email' => string, 'phone' => string]
     *                         - item_details: [['id' => string, 'price' => int, 'quantity' => int, 'name' => string]]
     * @return string Snap Token
     *
     * @throws MidtransApiException Jika API call gagal
     */
    public function getSnapToken(array $params): string
    {
        $orderId = $params['transaction_details']['order_id'] ?? null;
        $environment = $this->isProduction ? 'production' : 'sandbox';

        try {
            $this->validateSnapParams($params);

            // Log API call attempt
            PaymentLogger::logMidtransApiCall('snap/v1/transactions', $params, $environment);

            $snapToken = Snap::getSnapToken($params);

            // Log success
            PaymentLogger::logMidtransApiSuccess('snap/v1/transactions', $orderId, $environment);

            return $snapToken;

        } catch (\InvalidArgumentException $e) {
            // Validation error - log and rethrow
            PaymentLogger::logMidtransApiFailure(
                'snap/v1/transactions',
                $orderId,
                $e,
                $environment
            );

            throw MidtransApiException::snapTokenFailed(
                $orderId ?? 'unknown',
                $e->getMessage(),
                ['validation_error' => true],
                $e
            );

        } catch (\Exception $e) {
            // Log API failure with full context
            PaymentLogger::logMidtransApiFailure(
                'snap/v1/transactions',
                $orderId,
                $e,
                $environment,
                $this->extractHttpStatusCode($e)
            );

            // Determine error type and throw appropriate exception
            if ($this->isTimeoutError($e)) {
                throw MidtransApiException::timeout(
                    'snap/v1/transactions',
                    ['order_id' => $orderId],
                    $e
                );
            }

            if ($this->isNetworkError($e)) {
                throw MidtransApiException::networkError(
                    'snap/v1/transactions',
                    $e->getMessage(),
                    ['order_id' => $orderId],
                    $e
                );
            }

            throw MidtransApiException::snapTokenFailed(
                $orderId ?? 'unknown',
                $e->getMessage(),
                [],
                $e
            );
        }
    }

    /**
     * Get transaction status dari Midtrans API
     *
     * @param  string  $orderId  Order ID untuk dicek statusnya
     * @return array Transaction status data
     *
     * @throws MidtransApiException Jika API call gagal
     */
    public function getTransactionStatus(string $orderId): array
    {
        $environment = $this->isProduction ? 'production' : 'sandbox';

        try {
            PaymentLogger::logMidtransApiCall('v2/transactions/status', ['order_id' => $orderId], $environment);

            $status = Transaction::status($orderId);

            PaymentLogger::logMidtransApiSuccess('v2/transactions/status', $orderId, $environment);

            return (array) $status;

        } catch (\Exception $e) {
            PaymentLogger::logMidtransApiFailure(
                'v2/transactions/status',
                $orderId,
                $e,
                $environment,
                $this->extractHttpStatusCode($e)
            );

            if ($this->isTimeoutError($e)) {
                throw MidtransApiException::timeout(
                    'v2/transactions/status',
                    ['order_id' => $orderId],
                    $e
                );
            }

            if ($this->isNetworkError($e)) {
                throw MidtransApiException::networkError(
                    'v2/transactions/status',
                    $e->getMessage(),
                    ['order_id' => $orderId],
                    $e
                );
            }

            throw new MidtransApiException(
                "Failed to get transaction status for order {$orderId}: {$e->getMessage()}",
                'Gagal mengecek status pembayaran. Silakan coba lagi.',
                ['order_id' => $orderId],
                'v2/transactions/status',
                null,
                null,
                $e
            );
        }
    }

    /**
     * Verify notification signature dari Midtrans
     *
     * Signature dihitung dengan formula:
     * SHA512(order_id + status_code + gross_amount + server_key)
     *
     * Requirements: 11.3 - Log signature verification failures with IP address
     *
     * @param  array  $notification  Data notification dari Midtrans webhook
     * @return bool True jika signature valid, false jika tidak
     */
    public function verifySignature(array $notification): bool
    {
        $orderId = $notification['order_id'] ?? '';
        $statusCode = $notification['status_code'] ?? '';
        $grossAmount = $notification['gross_amount'] ?? '';
        $signatureKey = $notification['signature_key'] ?? '';

        if (empty($orderId) || empty($statusCode) || empty($grossAmount) || empty($signatureKey)) {
            PaymentLogger::logSignatureVerificationFailure($notification);

            Log::warning('Incomplete notification data for signature verification', [
                'order_id' => $orderId,
                'has_status_code' => ! empty($statusCode),
                'has_gross_amount' => ! empty($grossAmount),
                'has_signature_key' => ! empty($signatureKey),
                'ip_address' => request()->ip(),
            ]);

            return false;
        }

        // Calculate expected signature
        $expectedSignature = hash('sha512', $orderId.$statusCode.$grossAmount.$this->serverKey);

        $isValid = hash_equals($expectedSignature, $signatureKey);

        if (! $isValid) {
            // Log signature verification failure with full context
            PaymentLogger::logSignatureVerificationFailure($notification);
        }

        return $isValid;
    }

    /**
     * Get API base URL berdasarkan environment
     *
     * @return string Base URL untuk API calls
     */
    public function getApiBaseUrl(): string
    {
        return $this->isProduction ? self::PRODUCTION_BASE_URL : self::SANDBOX_BASE_URL;
    }

    /**
     * Get Snap base URL berdasarkan environment
     *
     * @return string Base URL untuk Snap
     */
    public function getSnapBaseUrl(): string
    {
        return $this->isProduction
            ? 'https://app.midtrans.com/snap/snap.js'
            : 'https://app.sandbox.midtrans.com/snap/snap.js';
    }

    /**
     * Check apakah dalam production mode
     */
    public function isProduction(): bool
    {
        return $this->isProduction;
    }

    /**
     * Get client key untuk frontend
     */
    public function getClientKey(): string
    {
        return $this->clientKey;
    }

    /**
     * Validate Snap Token request parameters
     *
     * @throws \InvalidArgumentException
     */
    protected function validateSnapParams(array $params): void
    {
        // Validate transaction_details
        if (! isset($params['transaction_details'])) {
            throw new \InvalidArgumentException('transaction_details is required');
        }

        $transactionDetails = $params['transaction_details'];

        if (empty($transactionDetails['order_id'])) {
            throw new \InvalidArgumentException('order_id is required in transaction_details');
        }

        if (! isset($transactionDetails['gross_amount']) || $transactionDetails['gross_amount'] <= 0) {
            throw new \InvalidArgumentException('gross_amount must be a positive number');
        }

        // Validate customer_details (optional but recommended)
        if (isset($params['customer_details'])) {
            $customerDetails = $params['customer_details'];

            if (isset($customerDetails['email']) && ! filter_var($customerDetails['email'], FILTER_VALIDATE_EMAIL)) {
                throw new \InvalidArgumentException('Invalid email format in customer_details');
            }
        }

        // Validate item_details (optional but recommended)
        if (isset($params['item_details'])) {
            foreach ($params['item_details'] as $index => $item) {
                if (! isset($item['price']) || $item['price'] < 0) {
                    throw new \InvalidArgumentException("Invalid price in item_details at index {$index}");
                }
                if (! isset($item['quantity']) || $item['quantity'] <= 0) {
                    throw new \InvalidArgumentException("Invalid quantity in item_details at index {$index}");
                }
            }
        }
    }

    /**
     * Check if exception is a timeout error
     */
    protected function isTimeoutError(\Throwable $e): bool
    {
        $message = strtolower($e->getMessage());

        return str_contains($message, 'timeout')
            || str_contains($message, 'timed out')
            || str_contains($message, 'operation timed out');
    }

    /**
     * Check if exception is a network error
     */
    protected function isNetworkError(\Throwable $e): bool
    {
        $message = strtolower($e->getMessage());

        return str_contains($message, 'could not resolve')
            || str_contains($message, 'connection refused')
            || str_contains($message, 'network is unreachable')
            || str_contains($message, 'failed to connect')
            || str_contains($message, 'curl error');
    }

    /**
     * Extract HTTP status code from exception if available
     */
    protected function extractHttpStatusCode(\Throwable $e): ?int
    {
        // Check if exception has getCode that returns HTTP status
        $code = $e->getCode();

        if ($code >= 100 && $code < 600) {
            return $code;
        }

        // Try to extract from message
        if (preg_match('/\b([45]\d{2})\b/', $e->getMessage(), $matches)) {
            return (int) $matches[1];
        }

        return null;
    }
}
