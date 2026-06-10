# Requirements Document

## Introduction

Sistem Payment Integration dengan Midtrans untuk Travel Package Booking System. Fitur ini memungkinkan customer melakukan pembayaran booking secara online melalui berbagai metode pembayaran yang disediakan Midtrans (Credit Card, Bank Transfer, E-Wallet, dll). Sistem akan otomatis mengupdate status booking berdasarkan status pembayaran dari Midtrans.

## Glossary

-   **Payment_System**: Sistem yang mengelola proses pembayaran booking menggunakan Midtrans payment gateway
-   **Booking**: Pesanan paket wisata yang dibuat oleh customer
-   **Midtrans**: Payment gateway third-party yang menyediakan berbagai metode pembayaran
-   **Snap_Token**: Token unik yang dihasilkan Midtrans untuk membuka payment page
-   **Transaction**: Record pembayaran yang terhubung dengan booking
-   **Payment_Notification**: Webhook callback dari Midtrans yang memberitahu status pembayaran
-   **Order_ID**: Identifier unik untuk setiap transaksi pembayaran
-   **Customer**: User yang melakukan booking dan pembayaran
-   **Admin**: User yang mengelola sistem melalui Filament admin panel

## Requirements

### Requirement 1: Inisiasi Pembayaran

**User Story:** As a customer, I want to initiate payment for my booking, so that I can complete my reservation and secure my travel package.

#### Acceptance Criteria

1. WHEN a customer creates a booking THEN THE Payment_System SHALL generate a unique Order_ID for the transaction
2. WHEN a customer views their pending booking THEN THE Payment_System SHALL display a "Pay Now" button
3. WHEN a customer clicks "Pay Now" THEN THE Payment_System SHALL request a Snap_Token from Midtrans API
4. WHEN Snap_Token is successfully generated THEN THE Payment_System SHALL open Midtrans payment page in a popup or redirect
5. WHEN Snap_Token generation fails THEN THE Payment_System SHALL display an error message and log the failure

### Requirement 2: Pembuatan Transaction Record

**User Story:** As a system, I want to create transaction records for each payment attempt, so that I can track payment history and status.

#### Acceptance Criteria

1. WHEN a Snap_Token is requested THEN THE Payment_System SHALL create a Transaction record with status "pending"
2. WHEN creating a Transaction THEN THE Payment_System SHALL store booking_id, order_id, amount, and snap_token
3. WHEN a Transaction is created THEN THE Payment_System SHALL link it to the corresponding Booking
4. THE Transaction SHALL store gross_amount matching the booking total_amount
5. THE Transaction SHALL store payment_type when available from Midtrans

### Requirement 3: Integrasi Midtrans Snap API

**User Story:** As a system, I want to integrate with Midtrans Snap API, so that I can provide secure payment processing.

#### Acceptance Criteria

1. WHEN requesting Snap_Token THEN THE Payment_System SHALL send transaction_details including order_id and gross_amount
2. WHEN requesting Snap_Token THEN THE Payment_System SHALL send customer_details including name, email, and phone
3. WHEN requesting Snap_Token THEN THE Payment_System SHALL send item_details describing the package being purchased
4. WHEN Midtrans API returns success THEN THE Payment_System SHALL store the snap_token
5. WHEN Midtrans API returns error THEN THE Payment_System SHALL handle the error gracefully and inform the customer
6. THE Payment_System SHALL use server_key from configuration for API authentication
7. THE Payment_System SHALL use correct Midtrans API endpoint based on environment (sandbox/production)

### Requirement 4: Payment Notification Handler

**User Story:** As a system, I want to receive and process payment notifications from Midtrans, so that I can update booking status automatically.

#### Acceptance Criteria

1. WHEN Midtrans sends a payment notification THEN THE Payment_System SHALL verify the notification signature
2. WHEN signature verification fails THEN THE Payment_System SHALL reject the notification and log the attempt
3. WHEN signature is valid THEN THE Payment_System SHALL extract order_id and transaction_status
4. WHEN transaction_status is "capture" or "settlement" THEN THE Payment_System SHALL update Transaction status to "paid"
5. WHEN transaction_status is "pending" THEN THE Payment_System SHALL update Transaction status to "pending"
6. WHEN transaction_status is "deny", "cancel", or "expire" THEN THE Payment_System SHALL update Transaction status to "failed"
7. WHEN Transaction status changes to "paid" THEN THE Payment_System SHALL update corresponding Booking status to "paid"
8. THE Payment_System SHALL respond with HTTP 200 to acknowledge notification receipt

### Requirement 5: Status Sinkronisasi Booking

**User Story:** As a system, I want to synchronize booking status with payment status, so that booking workflow reflects payment state accurately.

#### Acceptance Criteria

1. WHEN a Transaction status becomes "paid" THEN THE Payment_System SHALL update Booking status to "paid"
2. WHEN a Transaction status becomes "failed" THEN THE Payment_System SHALL keep Booking status as "pending"
3. WHEN multiple payment attempts exist for one Booking THEN THE Payment_System SHALL only update Booking when latest Transaction is "paid"
4. WHEN Booking status is updated to "paid" THEN THE Payment_System SHALL record payment_date timestamp
5. THE Payment_System SHALL prevent status downgrade from "paid" to "pending"

### Requirement 6: Payment Status Display

**User Story:** As a customer, I want to see my payment status, so that I know whether my payment was successful.

#### Acceptance Criteria

1. WHEN a customer views their booking THEN THE Payment_System SHALL display current payment status
2. WHEN payment is pending THEN THE Payment_System SHALL show "Pay Now" button
3. WHEN payment is paid THEN THE Payment_System SHALL show "Paid" badge and hide payment button
4. WHEN payment is failed THEN THE Payment_System SHALL show "Payment Failed" message and allow retry
5. WHEN displaying payment status THEN THE Payment_System SHALL show payment method used (if available)

### Requirement 7: Admin Payment Management

**User Story:** As an admin, I want to view and manage payment transactions, so that I can monitor payment activities and resolve issues.

#### Acceptance Criteria

1. WHEN admin views Booking detail THEN THE Payment_System SHALL display all related Transactions
2. WHEN admin views Transaction list THEN THE Payment_System SHALL show order_id, amount, status, and payment_date
3. WHEN admin views Transaction detail THEN THE Payment_System SHALL display full transaction data from Midtrans
4. THE Payment_System SHALL allow admin to manually check payment status from Midtrans API
5. THE Payment_System SHALL allow admin to view raw notification data for debugging

### Requirement 8: Payment Retry Mechanism

**User Story:** As a customer, I want to retry payment if my previous attempt failed, so that I can complete my booking without creating a new one.

#### Acceptance Criteria

1. WHEN a customer's payment fails THEN THE Payment_System SHALL allow creating a new payment attempt
2. WHEN customer retries payment THEN THE Payment_System SHALL generate a new Snap_Token
3. WHEN customer retries payment THEN THE Payment_System SHALL create a new Transaction record
4. WHEN customer retries payment THEN THE Payment_System SHALL use the same Booking
5. THE Payment_System SHALL mark previous failed Transaction as "superseded" when new attempt is created

### Requirement 9: Payment Expiration Handling

**User Story:** As a system, I want to handle payment expiration, so that unpaid bookings don't remain pending indefinitely.

#### Acceptance Criteria

1. WHEN a Transaction is created THEN THE Payment_System SHALL set expiry_time based on Midtrans configuration
2. WHEN payment expires THEN THE Payment_System SHALL update Transaction status to "expired"
3. WHEN Transaction expires THEN THE Payment_System SHALL allow customer to retry payment
4. THE Payment_System SHALL display time remaining for pending payments
5. WHEN all payment attempts expire THEN THE Payment_System SHALL keep Booking in "pending" status for admin review

### Requirement 10: Security dan Validation

**User Story:** As a system, I want to ensure payment security, so that transactions are protected from fraud and tampering.

#### Acceptance Criteria

1. WHEN receiving notification THEN THE Payment_System SHALL verify signature using SHA512 hash
2. WHEN processing payment THEN THE Payment_System SHALL validate order_id exists in database
3. WHEN processing payment THEN THE Payment_System SHALL validate gross_amount matches booking amount
4. WHEN amount mismatch is detected THEN THE Payment_System SHALL reject the payment and log the incident
5. THE Payment_System SHALL store Midtrans server_key securely in environment configuration
6. THE Payment_System SHALL use HTTPS for all Midtrans API communications
7. THE Payment_System SHALL sanitize all input from Midtrans notifications

### Requirement 11: Error Handling dan Logging

**User Story:** As a developer, I want comprehensive error handling and logging, so that I can troubleshoot payment issues effectively.

#### Acceptance Criteria

1. WHEN any Midtrans API call fails THEN THE Payment_System SHALL log the error with full context
2. WHEN notification processing fails THEN THE Payment_System SHALL log the failure without breaking the webhook
3. WHEN signature verification fails THEN THE Payment_System SHALL log the attempt with IP address
4. THE Payment_System SHALL log all payment status changes with timestamp
5. THE Payment_System SHALL store raw notification payload for debugging purposes

### Requirement 12: Configuration Management

**User Story:** As a developer, I want to configure Midtrans settings easily, so that I can switch between sandbox and production environments.

#### Acceptance Criteria

1. THE Payment_System SHALL read Midtrans server_key from environment variable
2. THE Payment_System SHALL read Midtrans client_key from environment variable
3. THE Payment_System SHALL read environment mode (sandbox/production) from configuration
4. WHEN in sandbox mode THEN THE Payment_System SHALL use Midtrans sandbox API endpoints
5. WHEN in production mode THEN THE Payment_System SHALL use Midtrans production API endpoints
6. THE Payment_System SHALL validate required configuration on application startup
