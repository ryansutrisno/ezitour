<?php

namespace App\Exceptions;

use Illuminate\Support\Facades\Log;

/**
 * Exception thrown when Midtrans API calls fail.
 * 
 * This exception handles all Midtrans API communication errors including:
 * - Network timeouts
 * - API errors
 * - Invalid responses
 * 
 * Requirements: 11.1 - Log all Midtrans API failures with full context
 */
class MidtransApiException extends PaymentException
{
    /**
     * API endpoint that failed
     */
    protected ?string $endpoint = null;

    /**
     * HTTP status code from API response
     */
    protected ?int $httpStatusCode = null;

    /**
     * Raw API response
     */
    protected mixed $apiResponse = null;

    /**
     * Create a new MidtransApiException instance.
     *
     * @param string $message Technical error message
     * @param string $userMessage User-friendly message
     * @param array $context Additional context
     * @param string|null $endpoint API endpoint
     * @param int|null $httpStatusCode HTTP status code
     * @param mixed $apiResponse Raw API response
     * @param \Throwable|null $previous Previous exception
     */
    public function __construct(
        string $message,
        string $userMessage = 'Layanan pembayaran sedang tidak tersedia. Silakan coba lagi nanti.',
        array $context = [],
        ?string $endpoint = null,
        ?int $httpStatusCode = null,
        mixed $apiResponse = null,
        ?\Throwable $previous = null
    ) {
        $this->endpoint = $endpoint;
        $this->httpStatusCode = $httpStatusCode;
        $this->apiResponse = $apiResponse;

        // Enrich context with API-specific data
        $enrichedContext = array_merge($context, [
            'api_endpoint' => $endpoint,
            'http_status_code' => $httpStatusCode,
            'api_response' => $this->sanitizeApiResponse($apiResponse),
            'environment' => config('midtrans.is_production') ? 'production' : 'sandbox',
        ]);

        parent::__construct($message, $userMessage, $enrichedContext, 502, $previous);
    }

    /**
     * Create exception for timeout errors
     *
     * @param string $endpoint
     * @param array $context
     * @param \Throwable|null $previous
     * @return static
     */
    public static function timeout(string $endpoint, array $context = [], ?\Throwable $previous = null): static
    {
        return new static(
            "Midtrans API timeout on endpoint: {$endpoint}",
            'Koneksi ke layanan pembayaran timeout. Silakan coba lagi.',
            $context,
            $endpoint,
            null,
            null,
            $previous
        );
    }

    /**
     * Create exception for network errors
     *
     * @param string $endpoint
     * @param string $errorMessage
     * @param array $context
     * @param \Throwable|null $previous
     * @return static
     */
    public static function networkError(string $endpoint, string $errorMessage, array $context = [], ?\Throwable $previous = null): static
    {
        return new static(
            "Midtrans API network error: {$errorMessage}",
            'Tidak dapat terhubung ke layanan pembayaran. Periksa koneksi internet Anda.',
            $context,
            $endpoint,
            null,
            null,
            $previous
        );
    }

    /**
     * Create exception for API error responses
     *
     * @param string $endpoint
     * @param int $httpStatusCode
     * @param mixed $apiResponse
     * @param array $context
     * @return static
     */
    public static function apiError(string $endpoint, int $httpStatusCode, mixed $apiResponse, array $context = []): static
    {
        $errorMessage = is_array($apiResponse) 
            ? ($apiResponse['error_messages'][0] ?? $apiResponse['status_message'] ?? 'Unknown error')
            : 'Unknown API error';

        return new static(
            "Midtrans API error ({$httpStatusCode}): {$errorMessage}",
            'Terjadi kesalahan pada layanan pembayaran. Silakan coba lagi.',
            $context,
            $endpoint,
            $httpStatusCode,
            $apiResponse
        );
    }

    /**
     * Create exception for Snap Token generation failure
     *
     * @param string $orderId
     * @param string $errorMessage
     * @param array $context
     * @param \Throwable|null $previous
     * @return static
     */
    public static function snapTokenFailed(string $orderId, string $errorMessage, array $context = [], ?\Throwable $previous = null): static
    {
        return new static(
            "Failed to generate Snap Token for order {$orderId}: {$errorMessage}",
            'Gagal membuat halaman pembayaran. Silakan coba lagi.',
            array_merge($context, ['order_id' => $orderId]),
            'snap/v1/transactions',
            null,
            null,
            $previous
        );
    }

    /**
     * Sanitize API response for logging (remove sensitive data)
     *
     * @param mixed $response
     * @return mixed
     */
    protected function sanitizeApiResponse(mixed $response): mixed
    {
        if (!is_array($response)) {
            return $response;
        }

        $sanitized = $response;
        
        // Remove or mask sensitive fields
        $sensitiveFields = ['signature_key', 'server_key', 'client_key'];
        
        foreach ($sensitiveFields as $field) {
            if (isset($sanitized[$field])) {
                $sanitized[$field] = substr($sanitized[$field], 0, 10) . '...';
            }
        }

        return $sanitized;
    }

    /**
     * Get API endpoint
     *
     * @return string|null
     */
    public function getEndpoint(): ?string
    {
        return $this->endpoint;
    }

    /**
     * Get HTTP status code
     *
     * @return int|null
     */
    public function getHttpStatusCode(): ?int
    {
        return $this->httpStatusCode;
    }
}
