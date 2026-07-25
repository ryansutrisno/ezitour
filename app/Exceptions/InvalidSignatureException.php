<?php

namespace App\Exceptions;

use Exception;

/**
 * Exception thrown when payment notification signature verification fails.
 *
 * This exception is used to indicate that a payment notification from Midtrans
 * has an invalid signature, which could indicate tampering or fraud.
 */
class InvalidSignatureException extends Exception
{
    /**
     * Create a new InvalidSignatureException instance.
     */
    public function __construct(
        string $message = 'Invalid notification signature',
        int $code = 403,
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
