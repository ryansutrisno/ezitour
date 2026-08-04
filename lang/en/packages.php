<?php

/*
|--------------------------------------------------------------------------
| Package page strings (English)
|--------------------------------------------------------------------------
| Natural, friendly travel-brand tone. Preserves Sprint 3 facet UI structure
| and Sprint 5/6 pricing/coupon semantics.
*/

return [

    // Packages index — header & search
    'index_title' => 'Search Travel Packages',
    'index_seo_title' => 'Travel Packages',
    'index_seo_description' => 'Explore the best travel packages in Indonesia with EziTour. Find your dream holiday today.',
    'index_eyebrow' => 'Explore',
    'index_title_main' => 'Find Your Adventure',
    'index_intro' => 'Choose from a wide range of curated travel packages for an unforgettable experience.',
    'search_placeholder' => 'Search packages or destinations...',

    // Facet sidebar (Sprint 3)
    'filter_title' => 'Filter Packages',
    'filter_region' => 'Region',
    'filter_category' => 'Category',
    'filter_duration' => 'Duration',
    'filter_active' => 'Active Filters',
    'filter_clear' => 'Clear',
    'filter_clear_all' => 'Clear Filters',
    'filter_all' => 'All',
    'filter_no_regions' => 'No regions listed yet.',
    'filter_no_categories' => 'No categories listed yet.',

    // Results
    'no_results_title' => 'No matching packages',
    'no_results_body' => 'Sorry, no packages match your search. Try adjusting or clearing your filters.',
    'no_results_cta' => 'View all packages',
    'price_from' => 'From',
    'result_detail' => 'Details',
    'duration_days' => ':days days',

    // Package detail (show)
    'show_breadcrumb_home' => 'Home',
    'show_breadcrumb_packages' => 'Packages',
    'show_itinerary_title' => 'Trip Itinerary',
    'show_facilities_title' => 'Included Facilities',
    'show_facility_car' => 'Air-Conditioned Car (Avanza/Innova/Hiace)',
    'show_facility_driver' => 'Experienced Driver + Fuel',
    'show_facility_ticket' => 'Attraction Entrance Tickets (Per Itinerary)',
    'show_facility_water' => 'Drinking Water',
    'show_booking_card_title' => 'Start Your Trip!',
    'show_duration_label' => 'Duration:',
    'show_destinations_count' => ':count destinations',
    'show_secure_note' => 'Secure & reliable payments via Midtrans',
    'show_facilities_included' => 'Included Facilities',

    // Reviews section (Sprint 4)
    'reviews_title' => 'Customer Reviews',
    'reviews_summary' => ':avg out of 5 (:count reviews)',
    'reviews_empty' => 'No reviews yet for this package.',
    'reviews_login_prompt_prefix' => '',
    'reviews_login_prompt_link' => 'Sign in',
    'reviews_login_prompt_suffix' => 'to leave a review.',
    'reviews_already_reviewed' => 'Thanks — you have already reviewed this package.',
    'reviews_no_paid_booking' => 'Book and complete this trip to leave a review.',
    'reviews_form_title' => 'Write Your Review',
    'reviews_form_rating_label' => 'Rating',
    'reviews_form_stars_suffix' => 'stars',
    'reviews_form_comment_label' => 'Comment',
    'reviews_form_comment_placeholder' => 'Share your experience...',
    'reviews_form_submit' => 'Submit Review',

    // Checkout (Sprint 5 + 6)
    'checkout_title' => 'Checkout',
    'checkout_breadcrumb_home' => 'Home',
    'checkout_breadcrumb_packages' => 'Packages',
    'checkout_booking_form_title' => 'Booking Details',
    'checkout_travel_date_label' => 'Travel Date',
    'checkout_participants_label' => 'Number of Participants',
    'checkout_participants_hint' => 'Maximum 50 participants per booking',
    'checkout_pickup_label' => 'Pickup Location',
    'checkout_pickup_placeholder' => 'e.g. Hotel Tentrem Yogyakarta, Jl. AM Sangaji No.72A...',
    'checkout_price_breakdown_title' => 'Price Breakdown',
    'checkout_price_per_pax' => 'Price per person',
    'checkout_participants_count' => 'Participants',
    'checkout_total' => 'Total Price',
    'checkout_summary_title' => 'Order Summary',
    'checkout_destinations_label' => 'Destinations',
    'checkout_more_destinations' => '+:count more destinations',
    'checkout_coupon_label' => 'Promo Code',
    'checkout_coupon_placeholder' => 'e.g. LIBURAN50',
    'checkout_coupon_remove' => 'Remove',
    'checkout_tier_discount' => 'Group Discount',
    'checkout_coupon_discount' => 'Promo Discount',
    'checkout_summary_total' => 'Total Payment',
    'checkout_continue_auth' => 'Continue →',
    'checkout_continue_payment' => 'Continue to Payment →',
    'checkout_guest_hint' => 'You will be asked to sign in or register after this step',
    'checkout_auth_section_title' => 'Sign In or Register',
    'checkout_auth_login_tab' => 'Sign In',
    'checkout_auth_register_tab' => 'Register',
    'checkout_auth_login_submit' => 'Sign In & Continue →',
    'checkout_auth_register_submit' => 'Register & Continue →',
    'checkout_auth_login_prompt' => "Don't have an account?",
    'checkout_auth_login_link' => 'Sign up now',
    'checkout_auth_register_prompt' => 'Already have an account?',
    'checkout_auth_register_link' => 'Sign in here',
    'checkout_password_mismatch' => 'Password and confirmation do not match',
    'checkout_back_to_package' => 'Back to Package Details',
    'checkout_tier_badge' => 'Save :amount (:percent%) — :tier',

    // Coupon JS messages (Sprint 6)
    'coupon_applied' => '🎉 Promo :code applied! You save :discount',
    'coupon_invalid' => 'Invalid promo code.',
    'coupon_validation_failed' => 'Failed to validate promo. Please try again.',
];
