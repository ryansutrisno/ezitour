# Requirements Document

## Introduction

Fitur ini mengubah flow booking paket wisata agar lebih mirip dengan checkout e-commerce. User dapat mengisi data booking terlebih dahulu, kemudian login/register jika belum memiliki akun, tanpa kehilangan data booking yang sudah diisi. Flow ini menghilangkan friction dimana user harus login dulu sebelum bisa melihat form booking.

## Glossary

-   **Checkout_Page**: Halaman yang menampilkan detail paket, form booking, dan form auth (login/register) dalam satu halaman
-   **Guest_Booking_Session**: Data booking sementara yang disimpan di session sebelum user login/register
-   **Booking_Form**: Form yang berisi tanggal perjalanan, jumlah peserta, dan lokasi penjemputan
-   **Auth_Section**: Bagian halaman checkout yang menampilkan form login atau register untuk guest user
-   **Authenticated_User**: User yang sudah login ke sistem

## Requirements

### Requirement 1: Halaman Checkout Terintegrasi

**User Story:** As a customer, I want to fill booking details and login/register on the same page, so that I don't lose my booking data when authenticating.

#### Acceptance Criteria

1. WHEN a user clicks "Pesan Sekarang" on a package detail page, THE Checkout_Page SHALL display the package details, Booking_Form, and Auth_Section (if guest) in a single page
2. WHEN a guest user fills the Booking_Form, THE System SHALL preserve the form data while the user completes authentication
3. WHEN an authenticated user visits the Checkout_Page, THE System SHALL hide the Auth_Section and show only the Booking_Form
4. THE Checkout_Page SHALL display package name, price per person, and total price calculation based on participant count

### Requirement 2: Guest Booking Session Management

**User Story:** As a guest user, I want my booking data to be preserved when I login or register, so that I don't have to re-enter the information.

#### Acceptance Criteria

1. WHEN a guest user submits the Booking_Form, THE System SHALL store the booking data in Guest_Booking_Session
2. WHEN a guest user successfully logs in or registers, THE System SHALL retrieve the Guest_Booking_Session data and create the booking automatically
3. WHEN a guest user successfully authenticates with pending booking data, THE System SHALL redirect to the payment page instead of dashboard
4. IF the Guest_Booking_Session expires or is invalid, THEN THE System SHALL redirect the user to the package detail page with an informative message

### Requirement 3: Inline Authentication

**User Story:** As a guest user, I want to login or register directly on the checkout page, so that I can complete my booking without navigating away.

#### Acceptance Criteria

1. THE Auth_Section SHALL provide tabs or toggle to switch between login and register forms
2. WHEN a guest user submits login credentials on the Checkout_Page, THE System SHALL authenticate the user without full page redirect
3. WHEN a guest user submits registration data on the Checkout_Page, THE System SHALL create the account and log them in automatically
4. IF authentication fails, THEN THE System SHALL display error messages inline without losing the Booking_Form data
5. WHEN authentication succeeds, THE System SHALL proceed to create the booking and redirect to payment

### Requirement 4: Booking Creation and Payment Redirect

**User Story:** As a customer, I want to proceed directly to payment after completing the checkout form, so that I can complete my purchase quickly.

#### Acceptance Criteria

1. WHEN an authenticated user submits the Booking_Form, THE System SHALL create the booking and redirect to the payment initiation
2. WHEN a guest user completes authentication with pending booking data, THE System SHALL create the booking and redirect to payment initiation
3. THE System SHALL validate all booking data before creating the booking record
4. IF booking creation fails, THEN THE System SHALL display an error message and allow the user to retry

### Requirement 5: Price Calculation Display

**User Story:** As a customer, I want to see the total price update as I change the number of participants, so that I know exactly how much I will pay.

#### Acceptance Criteria

1. WHEN a user changes the participant count in the Booking_Form, THE Checkout_Page SHALL update the total price display in real-time
2. THE Checkout_Page SHALL display price breakdown: price per person × number of participants = total price
3. THE System SHALL use the same price calculation logic for display and actual booking creation
