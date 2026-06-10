# Implementation Plan: Checkout Flow

## Overview

Implementasi checkout flow terintegrasi yang memungkinkan user mengisi data booking dan login/register dalam satu halaman, tanpa kehilangan data booking saat autentikasi.

## Tasks

-   [x] 1. Setup Services dan Routes

    -   [x] 1.1 Buat CheckoutSessionService untuk mengelola session data

        -   Buat file `app/Services/CheckoutSessionService.php`
        -   Implementasi methods: `store()`, `retrieve()`, `hasPendingBooking()`, `clear()`, `isValid()`
        -   Session key: `pending_booking`, TTL: 30 menit
        -   _Requirements: 2.1, 2.4_

    -   [x] 1.2 Buat BookingCreationService untuk membuat booking

        -   Buat file `app/Services/BookingCreationService.php`
        -   Implementasi methods: `createBooking()`, `createFromSession()`, `calculateTotalPrice()`
        -   _Requirements: 4.1, 4.2, 5.3_

    -   [x] 1.3 Tambahkan routes untuk checkout flow
        -   GET `/checkout/{slug}` - Halaman checkout
        -   POST `/checkout/{slug}` - Submit booking form
        -   POST `/checkout/{slug}/login` - Inline login
        -   POST `/checkout/{slug}/register` - Inline register
        -   _Requirements: 1.1, 3.2, 3.3_

-   [x] 2. Implementasi CheckoutController

    -   [x] 2.1 Buat CheckoutController dengan method show()

        -   Buat file `app/Http/Controllers/Front/CheckoutController.php`
        -   Load package by slug
        -   Pass auth state ke view
        -   _Requirements: 1.1, 1.3_

    -   [x] 2.2 Implementasi method store() untuk proses booking form

        -   Validasi input (travel_date, participants, pickup_location)
        -   Jika authenticated: buat booking dan redirect ke payment
        -   Jika guest: simpan ke session dan tampilkan auth forms
        -   _Requirements: 2.1, 4.1, 4.3_

    -   [x] 2.3 Implementasi method login() untuk inline authentication

        -   Validasi credentials
        -   Authenticate user
        -   Jika ada pending booking: buat booking dan redirect ke payment
        -   Jika gagal: return error dengan form data intact
        -   _Requirements: 3.2, 3.4, 3.5_

    -   [x] 2.4 Implementasi method register() untuk inline registration
        -   Validasi registration data
        -   Create user dan auto-login
        -   Jika ada pending booking: buat booking dan redirect ke payment
        -   Jika gagal: return error dengan form data intact
        -   _Requirements: 3.3, 3.4, 3.5_

-   [x] 3. Buat Checkout View

    -   [x] 3.1 Buat halaman checkout terintegrasi

        -   Buat file `resources/views/front/checkout/index.blade.php`
        -   Layout: 2 kolom (booking form kiri, package summary kanan)
        -   Tampilkan auth section jika guest
        -   _Requirements: 1.1, 1.3, 1.4_

    -   [x] 3.2 Implementasi booking form section

        -   Form fields: travel_date, participants, pickup_location
        -   Real-time price calculation dengan JavaScript
        -   Tampilkan validation errors
        -   _Requirements: 1.4, 5.1, 5.2_

    -   [x] 3.3 Implementasi auth section dengan tabs login/register
        -   Tab toggle antara login dan register form
        -   Login form: email, password
        -   Register form: name, email, password, password_confirmation
        -   Inline error display
        -   _Requirements: 3.1, 3.4_

-   [x] 4. Update Package Detail Page

    -   [x] 4.1 Ubah tombol "Pesan Sekarang" untuk redirect ke checkout page
        -   Ganti link dari form submit ke `/checkout/{slug}`
        -   Hapus form booking dari package detail page
        -   Tombol sama untuk guest dan authenticated user
        -   _Requirements: 1.1_

-   [x] 5. Update Auth Controllers untuk Handle Pending Booking

    -   [x] 5.1 Update LoginController untuk check pending booking setelah login

        -   Setelah login sukses, check `CheckoutSessionService::hasPendingBooking()`
        -   Jika ada: buat booking dan redirect ke payment
        -   Jika tidak: redirect ke dashboard seperti biasa
        -   _Requirements: 2.2, 2.3_

    -   [x] 5.2 Update RegisterController untuk check pending booking setelah register
        -   Setelah register sukses, check `CheckoutSessionService::hasPendingBooking()`
        -   Jika ada: buat booking dan redirect ke payment
        -   Jika tidak: redirect ke dashboard seperti biasa
        -   _Requirements: 2.2, 2.3_

-   [x] 6. Checkpoint - Ensure all tests pass

    -   Ensure all tests pass, ask the user if questions arise.

-   [ ]\* 7. Property-Based Tests

    -   [ ]\* 7.1 Write property test untuk session data persistence

        -   **Property 2: Session Data Persistence**
        -   Generate random booking data, verify session storage and retrieval
        -   **Validates: Requirements 1.2, 2.1**

    -   [ ]\* 7.2 Write property test untuk price calculation consistency
        -   **Property 6: Price Calculation Consistency**
        -   Generate random packages and participant counts
        -   Verify displayed price equals booking price
        -   **Validates: Requirements 5.1, 5.2, 5.3**

-   [ ]\* 8. Integration Tests

    -   [ ]\* 8.1 Write integration test untuk authenticated user checkout flow

        -   Test complete flow: checkout page → submit form → booking created → redirect to payment
        -   **Validates: Requirements 4.1**

    -   [ ]\* 8.2 Write integration test untuk guest user checkout flow (login path)

        -   Test complete flow: checkout page → submit form → login → booking created → redirect to payment
        -   **Validates: Requirements 2.2, 3.2, 3.5**

    -   [ ]\* 8.3 Write integration test untuk guest user checkout flow (register path)
        -   Test complete flow: checkout page → submit form → register → booking created → redirect to payment
        -   **Validates: Requirements 2.2, 3.3, 3.5**

-   [x] 9. Final Checkpoint
    -   Ensure all tests pass, ask the user if questions arise.
    -   Verify complete checkout flow works end-to-end

## Notes

-   Tasks marked with `*` are optional and can be skipped for faster MVP
-   Each task references specific requirements for traceability
-   Checkpoints ensure incremental validation
-   Property tests validate universal correctness properties
-   Integration tests validate complete user flows
