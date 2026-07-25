<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Support\Facades\Log;

/**
 * Base exception for payment-related errors.
 *
 * This exception provides comprehensive logging for all payment failures
 * including full context for debugging purposes.
 *
 * Requirements: 11.1, 11.2, 11.4
 */
class PaymentException extends Exception
{
    /**
     * Additional context data for logging
     */
    protected array $context = [];

    /**
     * User-friendly error message
     */
    protected string $userMessage;

    /**
     * Create a new PaymentException instance.
     *
     * @param  string  $message  Technical error message for logs
     * @param  string  $userMessage  User-friendly message to display
     * @param  array  $context  Additional context for logging
     * @param  int  $code  HTTP status code
     * @param  \Throwable|null  $previous  Previous exception
     */
    public function __construct(
        string $message,
        string $userMessage = 'Terjadi kesalahan pada pembayaran. Silakan coba lagi.',
        array $context = [],
        int $code = 500,
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);

        $this->userMessage = $userMessage;
        $this->context = array_merge($context, [
            'timestamp' => now()->toIso8601String(),
            'exception_class' => static::class,
        ]);

        // Auto-log when exception is created
        $this->logError();
    }

    /**
     * Get user-friendly error message
     */
    public function getUserMessage(): string
    {
        return $this->userMessage;
    }

    /**
     * Get context data
     */
    public function getContext(): array
    {
        return $this->context;
    }

    /**
     * Log the error with full context
     *
     * Requirements: 11.1, 11.4
     */
    protected function logError(): void
    {
        Log::error($this->getMessage(), array_merge($this->context, [
            'user_message' => $this->userMessage,
            'trace' => $this->getTraceAsString(),
        ]));
    }
}
