@props([
    'title' => null,
    'description' => null,
    'image' => null,
    'type' => 'website',
    'canonical' => null,
    'noindex' => false,
])

@php
    $general = app(App\Settings\GeneralSettings::class);
    $siteName = $general->siteName;
    $fallbackDescription = $general->tagline ?: 'EziTour — platform booking paket wisata terpercaya di Indonesia. Liburan impian tanpa ribet.';

    $description = $description ?: $fallbackDescription;
    $canonical = $canonical ?: request()->url();
    if ($image) {
        $image = Illuminate\Support\Str::startsWith($image, ['http://', 'https://']) ? $image : asset($image);
    }
@endphp

{{-- Meta description (SEO) --}}
<meta name="description" content="{{ Illuminate\Support\Str::limit($description, 160) }}">

{{-- Canonical --}}
<link rel="canonical" href="{{ $canonical }}">

{{-- Robots --}}
@if($noindex)
    <meta name="robots" content="noindex, nofollow">
@endif

{{-- Open Graph --}}
<meta property="og:site_name" content="{{ $siteName }}">
<meta property="og:type" content="{{ $type }}">
<meta property="og:title" content="{{ $title ?: $siteName }}">
<meta property="og:description" content="{{ Illuminate\Support\Str::limit($description, 160) }}">
<meta property="og:url" content="{{ $canonical }}">
@if($image)
    <meta property="og:image" content="{{ $image }}">
@endif

{{-- Twitter Card --}}
<meta name="twitter:card" content="{{ $image ? 'summary_large_image' : 'summary' }}">
<meta name="twitter:title" content="{{ $title ?: $siteName }}">
<meta name="twitter:description" content="{{ Illuminate\Support\Str::limit($description, 160) }}">
@if($image)
    <meta name="twitter:image" content="{{ $image }}">
@endif
