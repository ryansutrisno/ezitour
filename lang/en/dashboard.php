<?php

/*
|--------------------------------------------------------------------------
| Dashboard strings (English)
|--------------------------------------------------------------------------
| Natural tone mirroring the Indonesian dashboard.php.
*/

return [

    // Dashboard page
    'title' => 'My Dashboard',
    'seo_title' => 'My Dashboard',
    'greeting' => 'Hi, :name! 👋',
    'welcome' => 'Welcome to your travel dashboard.',
    'logout_button' => 'Sign Out',
    'orders_section_title' => 'Order History',
    'filter_all' => 'All',
    'filter_paid' => 'Paid',
    'filter_unpaid' => 'Unpaid',
    'retry_button' => 'Try Again',

    // Empty states
    'empty_paid_title' => 'No paid orders yet',
    'empty_paid_body' => 'Orders that have been paid in full will show up here.',
    'empty_unpaid_title' => 'No unpaid orders',
    'empty_unpaid_body' => 'All your orders are paid up. Sweet! 🎉',
    'empty_all_title' => 'No orders yet',
    'empty_all_body' => 'Start your adventure by booking your first travel package.',
    'empty_cta' => 'Browse Packages',

    // Booking card labels (Sprint 5 + 6)
    'booking_id' => 'Booking ID: #:id',
    'status_paid' => 'Paid',
    'status_pending_payment' => 'Awaiting Payment',
    'status_pending_payment_short' => 'Waiting',
    'status_failed' => 'Failed',
    'status_unpaid' => 'Unpaid',
    'status_completed' => 'Completed',
    'paid_at' => 'Paid :date',
    'time_remaining' => ':time left',
    'need_help' => 'Need help? Contact 24/7',
    'view_details' => 'View Details',
    'pay_now' => 'Pay Now',
    'continue_payment' => 'Continue Payment',
    'discount_percent' => 'Save :percent%',

    // Booking detail page (bookings/show)
    'detail_title' => 'Booking Details #:code',
    'detail_breadcrumb' => 'Booking Details #:code',
    'booking_code' => 'Booking Code',
    'status_pending' => 'Awaiting Payment',
    'status_cancelled' => 'Cancelled',
    'status_unknown' => 'Unknown',
    'package_section_title' => 'Package Details',
    'traveler_section_title' => 'Traveler Information',
    'transactions_section_title' => 'Payment History',
    'transactions_empty' => 'No payment transactions yet.',
    'summary_section_title' => 'Summary',
    'summary_status' => 'Status',
    'summary_travel_date' => 'Travel Date',
    'summary_subtotal' => 'Subtotal',
    'summary_tier_discount' => 'Group Discount',
    'summary_coupon_discount' => 'Promo Discount',
    'summary_total' => 'Total',
    'payment_method_label' => 'Method',
    'driver_label' => 'Driver',
    'car_label' => 'Car',
    'total_label' => 'Total',
    'promo_badge' => 'Promo',
    'transaction_pending_method' => 'Awaiting method',
    'cancel_confirm' => 'Are you sure you want to cancel this booking? This action cannot be undone.',
    'cancel_button' => 'Cancel Booking',
    'retry_payment_button' => 'Retry Payment',
    'download_ticket_button' => 'Download E-Ticket',
    'contact_support_button' => 'Contact Support',
    'cancelled_notice' => 'This booking has been cancelled.',
    'view_other_packages' => 'View Other Packages',
    'traveler_name' => 'Name',
    'traveler_email' => 'Email',
    'traveler_phone' => 'Phone',
    'txn_status_pending' => 'Pending',
    'txn_status_paid' => 'Paid',
    'txn_status_failed' => 'Failed',
    'txn_status_expired' => 'Expired',
    'txn_status_superseded' => 'Superseded',

    // Payments pay page
    'payment_title' => 'Payment',
    'payment_subtitle_retry' => 'Please try your payment again',
    'payment_subtitle_default' => 'Complete the payment for your booking',
    'payment_summary_title' => 'Booking Summary',
    'payment_order_id' => 'Order ID',
    'payment_package' => 'Travel Package',
    'payment_travel_date' => 'Travel Date',
    'payment_customer_name' => 'Booked By',
    'payment_total' => 'Total Payment',
    'payment_info_title' => 'Payment Information',
    'payment_info_step1' => 'Click "Pay Now" to choose a payment method',
    'payment_info_step2' => 'Pay with credit card, bank transfer, or e-wallet',
    'payment_info_step3' => 'Processed securely via Midtrans',
    'payment_button_hint' => 'Click the button above to choose a payment method',
    'payment_trust_badge' => 'Secure payment with Midtrans',
];
