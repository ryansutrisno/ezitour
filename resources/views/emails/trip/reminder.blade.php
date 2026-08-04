@component('mail::message')
# {{ __('emails.reminder_title') }} 🎒

{{ __('emails.reminder_greeting', ['name' => $booking->user->name, 'package' => $booking->package->name]) }} 🌊

@component('mail::panel')
**{{ __('emails.reminder_panel_title') }}**
- {{ __('emails.label_booking_code') }}: **{{ $bookingCode }}**
- {{ __('emails.label_package') }}: {{ $booking->package->name }}
- {{ __('emails.label_travel_date') }}: {{ $booking->travel_date->format('d F Y') }}
- {{ __('emails.label_pickup') }}: {{ $booking->pickup_location }}
- {{ __('emails.label_total') }}: Rp {{ number_format($totalAmount, 0, ',', '.') }}
- {{ __('emails.label_status') }}: **{{ __('emails.status_paid') }}** ✅
@endcomponent

{{ __('emails.reminder_tips_title') }}
- {{ __('emails.reminder_tip_documents') }}
- {{ __('emails.reminder_tip_clothing') }}
- {{ __('emails.reminder_tip_medicine') }}

{{ __('emails.reminder_contact_note') }}

@component('mail::button', ['url' => route('bookings.show', $booking)])
{{ __('emails.reminder_cta_button') }}
@endcomponent

{{ __('emails.reminder_closing') }}

{{ __('emails.signoff') }}
@endcomponent
