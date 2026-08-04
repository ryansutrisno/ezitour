@component('mail::message')
# {{ __('emails.success_title') }} 🎉

{{ __('emails.success_greeting', ['name' => $booking->user->name]) }}

@component('mail::panel')
**{{ __('emails.success_panel_title') }}**
- {{ __('emails.label_booking_code') }}: **{{ $bookingCode }}**
- {{ __('emails.label_package') }}: {{ $booking->package->name }}
- {{ __('emails.label_travel_date') }}: {{ $booking->travel_date->format('d/m/Y') }}
- {{ __('emails.label_payment_method') }}: {{ $transaction->payment_type ?? __('emails.payment_method_midtrans') }}
- {{ __('emails.label_total_paid') }}: Rp {{ number_format($totalAmount, 0, ',', '.') }}
- {{ __('emails.label_status') }}: **{{ __('emails.status_paid') }}** ✅
@endcomponent

{{ __('emails.success_note') }}

@component('mail::button', ['url' => route('dashboard.index')])
{{ __('emails.success_cta_button') }}
@endcomponent

{{ __('emails.success_closing') }}

{{ __('emails.signoff') }}
@endcomponent
