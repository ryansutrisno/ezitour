{{--
    Self-contained Ocean & Sand error layout.

    Renders with NO dependency on the app layout, Vite build, or compiled
    Tailwind tokens — all styling is inlined below using hex Ocean/Sand colours
    so it still renders during maintenance mode or mid-boot failures.
    Consumed via:
        @extends('errors.ocean', ['code' => '404', 'headline' => '…', 'description' => '…'])
--}}
@php
    $code = $code ?? '500';
    $headline = $headline ?? __('errors.default_headline');
    $description = $description ?? __('errors.default_description');
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <title>{{ $code }} - {{ $headline }} - EziTour</title>

    {{-- Fonts: Instrument Sans (body) + Plus Jakarta Sans (display) via Bunny Fonts --}}
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700|plus-jakarta-sans:600,700,800" rel="stylesheet">

    <style>
        *, *::before, *::after { box-sizing: border-box; }
        html, body { height: 100%; margin: 0; }
        body {
            font-family: 'Instrument Sans', ui-sans-serif, system-ui, -apple-system, 'Segoe UI', Roboto, sans-serif;
            color: #1e293b;
            background: #f8fafc;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            min-height: 100vh;
            position: relative;
            overflow: hidden;
        }
        .display { font-family: 'Plus Jakarta Sans', ui-sans-serif, system-ui, sans-serif; }

        /* Gradient mesh background (mirrors home/about hero) */
        .mesh::before,
        .mesh::after {
            content: "";
            position: fixed;
            border-radius: 9999px;
            filter: blur(90px);
            z-index: 0;
            pointer-events: none;
        }
        .mesh::before {
            width: 38rem; height: 38rem;
            top: -8rem; right: -8rem;
            background: rgba(31, 111, 224, 0.18);
        }
        .mesh::after {
            width: 32rem; height: 32rem;
            bottom: -8rem; left: -8rem;
            background: rgba(224, 138, 60, 0.16);
        }
        .grid-dots {
            position: fixed; inset: 0; z-index: 0; pointer-events: none;
            background-image: radial-gradient(circle at 1px 1px, rgba(15,23,42,0.05) 1px, transparent 0);
            background-size: 22px 22px;
        }

        .card {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 480px;
            background: #ffffff;
            border-radius: 24px;
            border: 1px solid #f1f5f9;
            box-shadow: 0 20px 50px -12px rgba(15, 23, 42, 0.18);
            overflow: hidden;
        }

        /* Gradient header strip (Ocean) */
        .card-header {
            background: linear-gradient(135deg, #1f6fe0 0%, #1759b5 100%);
            padding: 22px 28px;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .logo-mark {
            width: 36px; height: 36px;
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.18);
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }
        .logo-mark svg { width: 20px; height: 20px; }
        .logo-text {
            color: #ffffff;
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-weight: 800;
            font-size: 18px;
            letter-spacing: -0.02em;
        }
        .logo-text span { color: rgba(255, 255, 255, 0.65); }

        .card-body {
            padding: 40px 32px 32px;
            text-align: center;
        }

        /* Big error code with ocean→sand gradient text */
        .code {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-weight: 800;
            font-size: 96px;
            line-height: 1;
            letter-spacing: -0.04em;
            background: linear-gradient(135deg, #1f6fe0 0%, #e08a3c 100%);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            color: transparent;
            margin: 0 0 16px;
        }

        .headline {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-weight: 800;
            font-size: 24px;
            line-height: 1.2;
            letter-spacing: -0.02em;
            color: #0f172a;
            margin: 0 0 12px;
        }
        .description {
            font-size: 15px;
            line-height: 1.6;
            color: #475569;
            margin: 0 auto 28px;
            max-width: 360px;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 13px 28px;
            border-radius: 14px;
            background: linear-gradient(135deg, #1f6fe0 0%, #1759b5 100%);
            color: #ffffff;
            font-family: 'Instrument Sans', sans-serif;
            font-weight: 600;
            font-size: 15px;
            text-decoration: none;
            box-shadow: 0 8px 20px -6px rgba(31, 111, 224, 0.5);
            transition: transform 0.15s ease, box-shadow 0.15s ease;
        }
        .btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 12px 24px -6px rgba(31, 111, 224, 0.6);
        }
        .btn svg { width: 16px; height: 16px; }

        .footer-note {
            position: relative;
            z-index: 1;
            margin-top: 24px;
            font-size: 13px;
            color: #94a3b8;
            text-align: center;
        }

        @media (max-width: 480px) {
            .code { font-size: 72px; }
            .headline { font-size: 20px; }
            .card-body { padding: 32px 24px 28px; }
        }
    </style>
</head>
<body class="mesh">
    <div class="grid-dots"></div>

    <div class="card">
        <div class="card-header">
            <span class="logo-mark" aria-hidden="true">
                <svg fill="none" stroke="#ffffff" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </span>
            <span class="logo-text display">Ezi<span>Tour</span></span>
        </div>

        <div class="card-body">
            <div class="code display">{{ $code }}</div>
            <h1 class="headline display">{{ $headline }}</h1>
            <p class="description">{{ $description }}</p>

            <a href="{{ route('front.home') }}" class="btn">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                {{ __('errors.back_home_button') }}
            </a>
        </div>
    </div>

    <p class="footer-note">&copy; {{ date('Y') }} {{ __('errors.footer_note') }}</p>
</body>
</html>
