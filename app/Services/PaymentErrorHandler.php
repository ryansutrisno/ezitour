<?php

namespace App\Services;

use App\Exceptions\MidtransApiException;
use App\Exceptions\NotificationProcessingException;
use App\Exceptions\PaymentException;
use Illuminate\Support\Facades\Log;

/**
 * PaymentErrorHandler - Centralized error handling for payment operations
 * 
 * This service provides user-friendly error messages for different error types
 * and handles API timeouts and network errors gracefully.
 * 
 * Requirements: 1.5, 3.5
 */
class PaymentErrorHandler
{
    /**
     * Error type constants
     */
    public const ERROR_TYPE_TIMEOUT = 'timeout';
    public const ERROR_TYPE_NETWORK = 'network';
    public const ERROR_TYPE_API = 'api';
    public const ERROR_TYPE_VALIDATION = 'validation';
    public const ERROR_TYPE_BOOKING = 'booking';
    public const ERROR_TYPE_UNKNOWN = 'unknown';

    /**
     * User-friendly error messages for different error types
     */
    protected static array $errorMessages = [
        self::ERROR_TYPE_TIMEOUT => [
            'title' => 'Koneksi Timeout',
            'message' => 'Koneksi ke layanan pembayaran timeout. Silakan coba lagi dalam beberapa saat.',
            'suggestion' => 'Periksa koneksi internet Anda dan coba lagi.',
        ],
        self::ERROR_TYPE_NETWORK => [
            'title' => 'Koneksi Gagal',
            'message' => 'Tidak dapat terhubung ke layanan pembayaran.',
            'suggestion' => 'Pastikan Anda terhubung ke internet dan coba lagi.',
        ],
        self::ERROR_TYPE_API => [
            'title' => 'Layanan Tidak Tersedia',
            'message' => 'Layanan pembayaran sedang tidak tersedia.',
            'suggestion' => 'Silakan coba lagi dalam beberapa menit.',
        ],
        self::ERROR_TYPE_VALIDATION => [
            'title' => 'Data Tidak Valid',
            'message' => 'Data pembayaran tidak valid.',
            'suggestion' => 'Periksa kembali data booking Anda.',
        ],
        self::ERROR_TYPE_BOOKING => [
            'title' => 'Booking Tidak Valid',
            'message' => 'Booking tidak dapat diproses untuk pembayaran.',
            'suggestion' => 'Hubungi customer service untuk bantuan.',
        ],
        self::ERROR_TYPE_UNKNOWN => [
            'title' => 'Terjadi Kesalahan',
            'message' => 'Terjadi kesalahan saat memproses pembayaran.',
            'suggestion' => 'Silakan coba lagi atau hubungi customer service.',
        ],
    ];

    /**
     * Get user-friendly error response from exception
     *
     * @param \Throwable $exception
     * @return array
     */
    public static function getErrorResponse(\Throwable $exception): array
    {
        $errorType = self::determineErrorType($exception);
        $errorInfo = self::$errorMessages[$errorType] ?? self::$errorMessages[self::ERROR_TYPE_UNKNOWN];

        // Get specific message from PaymentException if available
        $specificMessage = null;
        if ($exception instanceof PaymentException) {
            $specificMessage = $exception->getUserMessage();
        }

        return [
            'success' => false,
            'error_type' => $errorType,
            'title' => $errorInfo['title'],
            'message' => $specificMessage ?? $errorInfo['message'],
            'suggestion' => $errorInfo['suggestion'],
            'can_retry' => self::canRetry($errorType),
        ];
    }

    /**
     * Determine error type from exception
     *
     * @param \Throwable $exception
     * @return string
     */
    public static function determineErrorType(\Throwable $exception): string
    {
        // Check for MidtransApiException subtypes
        if ($exception instanceof MidtransApiException) {
            $message = strtolower($exception->getMessage());
            
            if (str_contains($message, 'timeout')) {
                return self::ERROR_TYPE_TIMEOUT;
            }
            
            if (str_contains($message, 'network') || str_contains($message, 'connect')) {
                return self::ERROR_TYPE_NETWORK;
            }
            
            return self::ERROR_TYPE_API;
        }

        // Check for NotificationProcessingException
        if ($exception instanceof NotificationProcessingException) {
            return self::ERROR_TYPE_API;
        }

        // Check for PaymentException
        if ($exception instanceof PaymentException) {
            $message = strtolower($exception->getMessage());
            
            if (str_contains($message, 'booking')) {
                return self::ERROR_TYPE_BOOKING;
            }
            
            if (str_contains($message, 'valid')) {
                return self::ERROR_TYPE_VALIDATION;
            }
            
            return self::ERROR_TYPE_API;
        }

        // Check for generic timeout/network errors
        $message = strtolower($exception->getMessage());
        
        if (str_contains($message, 'timeout') || str_contains($message, 'timed out')) {
            return self::ERROR_TYPE_TIMEOUT;
        }
        
        if (str_contains($message, 'could not resolve') || 
            str_contains($message, 'connection refused') ||
            str_contains($message, 'network')) {
            return self::ERROR_TYPE_NETWORK;
        }

        return self::ERROR_TYPE_UNKNOWN;
    }

    /**
     * Check if error type allows retry
     *
     * @param string $errorType
     * @return bool
     */
    public static function canRetry(string $errorType): bool
    {
        return in_array($errorType, [
            self::ERROR_TYPE_TIMEOUT,
            self::ERROR_TYPE_NETWORK,
            self::ERROR_TYPE_API,
            self::ERROR_TYPE_UNKNOWN,
        ]);
    }

    /**
     * Get user-friendly message for display
     *
     * @param \Throwable $exception
     * @return string
     */
    public static function getUserMessage(\Throwable $exception): string
    {
        // If it's a PaymentException, use its user message
        if ($exception instanceof PaymentException) {
            return $exception->getUserMessage();
        }

        // Otherwise, determine error type and return appropriate message
        $errorType = self::determineErrorType($exception);
        $errorInfo = self::$errorMessages[$errorType] ?? self::$errorMessages[self::ERROR_TYPE_UNKNOWN];

        return $errorInfo['message'];
    }

    /**
     * Get error title for display
     *
     * @param \Throwable $exception
     * @return string
     */
    public static function getErrorTitle(\Throwable $exception): string
    {
        $errorType = self::determineErrorType($exception);
        $errorInfo = self::$errorMessages[$errorType] ?? self::$errorMessages[self::ERROR_TYPE_UNKNOWN];

        return $errorInfo['title'];
    }

    /**
     * Get suggestion for user
     *
     * @param \Throwable $exception
     * @return string
     */
    public static function getSuggestion(\Throwable $exception): string
    {
        $errorType = self::determineErrorType($exception);
        $errorInfo = self::$errorMessages[$errorType] ?? self::$errorMessages[self::ERROR_TYPE_UNKNOWN];

        return $errorInfo['suggestion'];
    }

    /**
     * Handle exception and return JSON response
     *
     * @param \Throwable $exception
     * @param int $statusCode
     * @return \Illuminate\Http\JsonResponse
     */
    public static function jsonResponse(\Throwable $exception, int $statusCode = 500): \Illuminate\Http\JsonResponse
    {
        return response()->json(self::getErrorResponse($exception), $statusCode);
    }

    /**
     * Handle exception and return redirect with error message
     *
     * @param \Throwable $exception
     * @param string $route
     * @return \Illuminate\Http\RedirectResponse
     */
    public static function redirectWithError(\Throwable $exception, string $route): \Illuminate\Http\RedirectResponse
    {
        $errorResponse = self::getErrorResponse($exception);
        
        return redirect()->route($route)
            ->with('error', $errorResponse['message'])
            ->with('error_title', $errorResponse['title'])
            ->with('error_suggestion', $errorResponse['suggestion'])
            ->with('can_retry', $errorResponse['can_retry']);
    }

    /**
     * Format error for flash message
     *
     * @param \Throwable $exception
     * @return string
     */
    public static function formatFlashMessage(\Throwable $exception): string
    {
        $errorResponse = self::getErrorResponse($exception);
        
        return $errorResponse['message'] . ' ' . $errorResponse['suggestion'];
    }
}
