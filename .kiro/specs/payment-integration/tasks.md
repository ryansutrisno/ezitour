# Implementation Plan: Payment Integration

## Overview

Implementation plan untuk mengintegrasikan Midtrans payment gateway ke dalam Travel Package Booking System. Tasks disusun secara incremental, dimulai dari setup dan konfigurasi, kemudian core payment logic, notification handling, UI integration, dan terakhir admin features. Setiap task membangun di atas task sebelumnya untuk memastikan progress yang terukur.

## Tasks

-   [x] 1. Setup dan Konfigurasi Midtrans

    -   Install Midtrans PHP SDK via Composer
    -   Create configuration file `config/midtrans.php`
    -   Add environment variables to `.env.example`
    -   Validate configuration on application startup
    -   _Requirements: 12.1, 12.2, 12.3, 12.6_

-   [-] 2. Create Transaction Model dan Migration

    -   [x] 2.1 Create migration for transactions table

        -   Create migration file with complete schema
        -   Add foreign key to bookings table
        -   Add indexes for order_id and booking_id
        -   Add enum for transaction_status
        -   _Requirements: 2.1, 2.2, 2.3_

    -   [x] 2.2 Create Transaction model

        -   Define fillable attributes
        -   Add relationship to Booking model
        -   Add casts for json fields
        -   Add status constants
        -   _Requirements: 2.1, 2.2, 2.3_

    -   [ ]\* 2.3 Write property test for Transaction model

        -   **Property 3: Amount Consistency**
        -   **Validates: Requirements 2.4, 10.3**

    -   [x] 2.4 Update Booking model

        -   Add transactions() relationship
        -   Add latestTransaction() relationship
        -   Add isPaid() helper method
        -   Add hasPendingPayment() helper method
        -   Add payment_date column to bookings migration
        -   _Requirements: 5.4_

    -   [ ]\* 2.5 Write unit tests for Booking payment methods
        -   Test isPaid() returns true when status is paid
        -   Test hasPendingPayment() detects pending transactions
        -   _Requirements: 5.1, 5.2_

-   [x] 3. Checkpoint - Database Setup Complete

    -   Run migrations and verify tables created
    -   Ensure all tests pass
    -   Ask the user if questions arise

-   [-] 4. Implement MidtransClient Service

    -   [x] 4.1 Create MidtransClient class

        -   Initialize Midtrans configuration
        -   Implement getSnapToken() method
        -   Implement getTransactionStatus() method
        -   Implement verifySignature() method
        -   Handle environment-based endpoint selection
        -   _Requirements: 3.1, 3.2, 3.3, 3.4, 3.7_

    -   [ ]\* 4.2 Write property test for environment-based endpoints

        -   **Property 5: Environment-Based Endpoint Selection**
        -   **Validates: Requirements 3.7, 12.4, 12.5**

    -   [ ]\* 4.3 Write property test for signature verification

        -   **Property 6: Signature Verification Requirement**
        -   **Validates: Requirements 4.1, 10.1**

    -   [ ]\* 4.4 Write unit tests for MidtransClient
        -   Test getSnapToken with valid parameters
        -   Test getSnapToken handles API errors
        -   Test verifySignature with valid signature
        -   Test verifySignature with invalid signature
        -   _Requirements: 3.4, 3.5, 4.1, 4.2_

-   [-] 5. Implement PaymentService Core Logic

    -   [x] 5.1 Create PaymentService class

        -   Inject MidtransClient dependency
        -   Implement generateOrderId() method
        -   Implement createPayment() method
        -   Implement buildSnapParams() helper method
        -   Handle Snap Token generation errors
        -   _Requirements: 1.1, 1.3, 2.1, 3.1, 3.2, 3.3_

    -   [ ]\* 5.2 Write property test for order ID uniqueness

        -   **Property 1: Order ID Uniqueness**
        -   **Validates: Requirements 1.1**

    -   [ ]\* 5.3 Write property test for transaction creation completeness

        -   **Property 2: Transaction Creation Completeness**
        -   **Validates: Requirements 2.1, 2.2, 2.3**

    -   [ ]\* 5.4 Write property test for API request structure

        -   **Property 4: Midtrans API Request Structure**
        -   **Validates: Requirements 3.1, 3.2, 3.3**

    -   [ ]\* 5.5 Write unit tests for PaymentService createPayment
        -   Test successful payment creation
        -   Test with invalid booking
        -   Test API error handling
        -   _Requirements: 1.3, 1.5, 3.4, 3.5_

-   [-] 6. Implement Payment Notification Handler

    -   [x] 6.1 Add processNotification() method to PaymentService

        -   Verify notification signature
        -   Extract order_id and transaction_status
        -   Map Midtrans status to internal status
        -   Update Transaction status
        -   Update Booking status when paid
        -   Store raw notification data
        -   _Requirements: 4.1, 4.3, 4.4, 4.6, 4.7, 11.5_

    -   [ ]\* 6.2 Write property test for invalid signature rejection

        -   **Property 7: Invalid Signature Rejection**
        -   **Validates: Requirements 4.2, 11.3**

    -   [ ]\* 6.3 Write property test for payment status mapping

        -   **Property 8: Payment Status Mapping**
        -   **Validates: Requirements 4.4, 4.6**

    -   [ ]\* 6.4 Write property test for cascading status update

        -   **Property 9: Cascading Status Update**
        -   **Validates: Requirements 4.7, 5.1, 5.4**

    -   [ ]\* 6.5 Write property test for failed payment isolation

        -   **Property 10: Failed Payment Isolation**
        -   **Validates: Requirements 5.2**

    -   [ ]\* 6.6 Write unit tests for notification processing
        -   Test with capture status
        -   Test with settlement status
        -   Test with deny status
        -   Test with invalid signature
        -   Test with missing order_id
        -   _Requirements: 4.1, 4.2, 4.3, 4.4, 4.6_

-   [x] 7. Implement Status Synchronization Logic

    -   [x] 7.1 Add status update logic to PaymentService

        -   Implement updateBookingStatus() method
        -   Check for latest transaction priority
        -   Prevent status downgrade from paid to pending
        -   Record payment_date timestamp
        -   _Requirements: 5.1, 5.3, 5.4, 5.5_

    -   [ ]\* 7.2 Write property test for latest transaction priority

        -   **Property 11: Latest Transaction Priority**
        -   **Validates: Requirements 5.3**

    -   [ ]\* 7.3 Write property test for status immutability

        -   **Property 12: Status Immutability**
        -   **Validates: Requirements 5.5**

    -   [ ]\* 7.4 Write unit tests for status synchronization
        -   Test booking updated when transaction paid
        -   Test booking not updated when transaction failed
        -   Test multiple transactions scenario
        -   Test paid booking cannot be downgraded
        -   _Requirements: 5.1, 5.2, 5.3, 5.5_

-   [x] 8. Checkpoint - Core Payment Logic Complete

    -   Run all tests and ensure they pass
    -   Test payment creation manually with sandbox
    -   Ask the user if questions arise

-   [x] 9. Create Payment Controller

    -   [x] 9.1 Create PaymentController

        -   Implement create() method for payment initiation
        -   Implement finish() callback method
        -   Implement unfinish() callback method
        -   Implement error() callback method
        -   Add authentication middleware
        -   Validate booking ownership
        -   _Requirements: 1.2, 1.3, 1.4_

    -   [ ]\* 9.2 Write unit tests for PaymentController
        -   Test create endpoint with valid booking
        -   Test create endpoint with unauthorized user
        -   Test create endpoint with already paid booking
        -   Test callback endpoints
        -   _Requirements: 1.2, 1.3_

-   [x] 10. Create Notification Webhook Controller

    -   [x] 10.1 Create MidtransNotificationController

        -   Implement handle() method for webhook
        -   Call PaymentService to process notification
        -   Return HTTP 200 response
        -   Handle exceptions gracefully
        -   Add CSRF exemption for webhook route
        -   _Requirements: 4.1, 4.8_

    -   [ ]\* 10.2 Write property test for webhook response consistency

        -   **Property 13: Webhook Response Consistency**
        -   **Validates: Requirements 4.8**

    -   [ ]\* 10.3 Write property test for order ID validation

        -   **Property 16: Order ID Validation**
        -   **Validates: Requirements 10.2**

    -   [ ]\* 10.4 Write property test for amount mismatch detection

        -   **Property 17: Amount Mismatch Detection**
        -   **Validates: Requirements 10.4**

    -   [ ]\* 10.5 Write unit tests for notification webhook
        -   Test with valid notification
        -   Test with invalid signature
        -   Test with non-existent order_id
        -   Test with amount mismatch
        -   Test error handling
        -   _Requirements: 4.1, 4.2, 10.2, 10.4_

-   [x] 11. Add Payment Routes

    -   Define payment routes in web.php
    -   Add authentication middleware
    -   Add CSRF exemption for webhook
    -   _Requirements: 1.2, 1.3, 4.1_

-   [x] 12. Implement Payment Retry Logic

    -   [x] 12.1 Add retryPayment() method to PaymentService

        -   Check if booking has failed/expired transaction
        -   Mark previous transaction as superseded
        -   Create new transaction
        -   Generate new Snap Token
        -   _Requirements: 8.1, 8.2, 8.3, 8.4, 8.5_

    -   [ ]\* 12.2 Write property test for payment retry

        -   **Property 14: Payment Retry Creates New Transaction**
        -   **Validates: Requirements 8.1, 8.2, 8.3, 8.4, 8.5**

    -   [ ]\* 12.3 Write unit tests for retry payment
        -   Test retry after failed payment
        -   Test retry after expired payment
        -   Test retry marks old transaction as superseded
        -   Test retry uses same booking
        -   _Requirements: 8.1, 8.2, 8.3, 8.4, 8.5_

-   [x] 13. Implement Payment Expiration Handling

    -   [x] 13.1 Add expiration logic to PaymentService

        -   Set expiry_time when creating transaction
        -   Add method to check and update expired transactions
        -   Allow retry for expired transactions
        -   _Requirements: 9.1, 9.2, 9.3_

    -   [ ]\* 13.2 Write property test for transaction expiry

        -   **Property 15: Transaction Expiry Handling**
        -   **Validates: Requirements 9.2, 9.3**

    -   [ ]\* 13.3 Write unit tests for expiration handling
        -   Test expiry_time is set on creation
        -   Test expired transaction status update
        -   Test retry allowed after expiration
        -   _Requirements: 9.1, 9.2, 9.3_

-   [x] 14. Checkpoint - Payment Flow Complete

    -   Test complete payment flow end-to-end
    -   Test retry flow
    -   Test expiration handling
    -   Ensure all tests pass
    -   Ask the user if questions arise

-   [x] 15. Create Customer Payment UI

    -   [x] 15.1 Update booking detail view

        -   Add payment status display
        -   Add "Pay Now" button for pending bookings
        -   Add "Paid" badge for paid bookings
        -   Add "Payment Failed" message with retry button
        -   Display payment method if available
        -   Display time remaining for pending payments
        -   _Requirements: 1.2, 6.1, 6.2, 6.3, 6.4, 6.5, 9.4_

    -   [x] 15.2 Create payment page view

        -   Display booking summary
        -   Display total amount
        -   Add button to trigger Snap popup
        -   Handle Snap Token loading
        -   _Requirements: 1.4_

    -   [x] 15.3 Add JavaScript for Midtrans Snap

        -   Include Midtrans Snap.js library
        -   Implement Snap popup trigger
        -   Handle payment callbacks (finish, unfinish, error)
        -   _Requirements: 1.4_

    -   [ ]\* 15.4 Write property test for conditional UI rendering

        -   **Property 21: Conditional UI Rendering**
        -   **Validates: Requirements 1.2, 6.2, 6.3, 6.4**

    -   [ ]\* 15.5 Write property test for payment method display
        -   **Property 22: Payment Method Display**
        -   **Validates: Requirements 2.5, 6.5**

-   [x] 16. Update Dashboard to Show Payment Status

    -   Update dashboard booking list to show payment status
    -   Add filter for paid/unpaid bookings
    -   Add payment status badges
    -   _Requirements: 6.1_

-   [x] 17. Implement Error Handling and Logging

    -   [x] 17.1 Add comprehensive error logging

        -   Log all Midtrans API failures
        -   Log notification processing failures
        -   Log signature verification failures
        -   Log payment status changes
        -   Include full context in logs
        -   _Requirements: 11.1, 11.2, 11.3, 11.4_

    -   [ ]\* 17.2 Write property test for error logging completeness

        -   **Property 19: Error Logging Completeness**
        -   **Validates: Requirements 11.1, 11.2, 11.4**

    -   [ ]\* 17.3 Write property test for raw notification persistence

        -   **Property 20: Raw Notification Persistence**
        -   **Validates: Requirements 11.5**

    -   [x] 17.2 Add user-friendly error messages

        -   Display appropriate messages for different error types
        -   Handle API timeouts gracefully
        -   Handle network errors
        -   _Requirements: 1.5, 3.5_

    -   [ ]\* 17.4 Write property test for graceful error handling
        -   **Property 23: Error Handling Gracefully**
        -   **Validates: Requirements 1.5, 3.5**

-   [x] 18. Create Filament Admin Resources for Transactions

    -   [x] 18.1 Create TransactionResource

        -   Add table columns for order_id, amount, status, payment_date
        -   Add filters for transaction status
        -   Add relationship to booking
        -   Display payment_type
        -   _Requirements: 7.2_

    -   [x] 18.2 Add transaction detail view

        -   Display full transaction data
        -   Display raw notification JSON
        -   Add button to check status from Midtrans API
        -   _Requirements: 7.3, 7.4, 7.5_

    -   [x] 18.3 Update BookingResource
        -   Add transactions relation manager
        -   Display payment status in booking list
        -   Show payment_date when available
        -   _Requirements: 7.1_

-   [-] 19. Implement Security Features

    -   [ ]\* 19.1 Write property test for input sanitization

        -   **Property 18: Input Sanitization**
        -   **Validates: Requirements 10.7**

    -   [x] 19.2 Add rate limiting to webhook endpoint

        -   Limit notification requests per IP
        -   Log excessive requests
        -   _Requirements: 4.2_

    -   [ ]\* 19.3 Write unit tests for security features
        -   Test HTTPS enforcement
        -   Test input sanitization
        -   Test rate limiting
        -   _Requirements: 10.6, 10.7_

-   [x] 20. Add Manual Payment Status Check Feature

    -   [x] 20.1 Add checkPaymentStatus() method to PaymentService

        -   Call Midtrans API to get current status
        -   Update local transaction status
        -   Return status information
        -   _Requirements: 7.4_

    -   [x] 20.2 Add admin action to check payment status
        -   Add button in TransactionResource
        -   Display result to admin
        -   Log manual check attempts
        -   _Requirements: 7.4_

-   [x] 21. Final Checkpoint - Complete Integration

    -   Run full test suite
    -   Test complete flow in sandbox environment
    -   Verify all error scenarios handled
    -   Verify admin features working
    -   Ensure all tests pass
    -   Ask the user if questions arise

-   [x] 22. Documentation and Configuration

    -   [x] 22.1 Update .env.example with Midtrans variables

        -   Add MIDTRANS_SERVER_KEY
        -   Add MIDTRANS_CLIENT_KEY
        -   Add MIDTRANS_IS_PRODUCTION
        -   Add comments explaining each variable
        -   _Requirements: 12.1, 12.2, 12.3_

    -   [x] 22.2 Create deployment checklist

        -   List required environment variables
        -   List required migrations
        -   List webhook URL configuration in Midtrans dashboard
        -   List testing steps
        -   _Requirements: 12.6_

    -   [ ]\* 22.3 Write integration tests for complete payment flow
        -   Test booking creation → payment → notification → status update
        -   Test retry flow
        -   Test expiration flow
        -   _Requirements: All_

## Notes

-   Tasks marked with `*` are optional and can be skipped for faster MVP
-   Each task references specific requirements for traceability
-   Property tests validate universal correctness properties
-   Unit tests validate specific examples and edge cases
-   Checkpoints ensure incremental validation
-   Use Laravel's database transactions for test isolation
-   Mock Midtrans API calls in tests to avoid external dependencies
