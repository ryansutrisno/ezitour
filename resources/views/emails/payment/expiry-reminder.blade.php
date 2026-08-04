@component('mail::message')
# {{ __('emails.expiry_title') }} ⏰

{{ __('emails.expiry_greeting', ['name' => $booking->user->name]) }}

@component('mail::panel')
**{{ __('emails.expiry_panel_title') }}**
- {{ __('emails.label_booking_code') }}: **{{ $bookingCode }}**
- {{ __('emails.label_package') }}: {{ $booking->package->name }}
- {{ __('emails.label_travel_date') }}: {{ $booking->travel_date->format('d F Y') }}
- {{ __('emails.label_total') }}: Rp {{ number_format($totalAmount, 0, ',', '.') }}
- {{ __('emails.label_status') }}: {{ __('emails.status_pending_payment') }}
@endcomponent

{{ __('emails.expiry_note') }}

@component('mail::button', ['url' => route('dashboard.index')])
{{ __('emails.expiry_cta_button') }}
@endcomponent

{{ __('emails.expiry_help') }}

{{ __('emails.signoff') }}
@endcomponent
