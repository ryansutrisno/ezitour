@component('mail::message')
# {{ __('emails.failed_title') }}

{{ __('emails.failed_greeting', ['name' => $booking->user->name]) }}

@component('mail::panel')
**{{ __('emails.failed_panel_title') }}**
- {{ __('emails.label_booking_code') }}: **{{ $bookingCode }}**
- {{ __('emails.label_package') }}: {{ $booking->package->name }}
- {{ __('emails.label_total') }}: Rp {{ number_format($totalAmount, 0, ',', '.') }}
- {{ __('emails.label_status') }}: {{ __('emails.status_failed') }}
@endcomponent

{{ __('emails.failed_note') }}

@component('mail::button', ['url' => route('dashboard.index')])
{{ __('emails.failed_cta_button') }}
@endcomponent

{{ __('emails.failed_help') }}

{{ __('emails.signoff') }}
@endcomponent
