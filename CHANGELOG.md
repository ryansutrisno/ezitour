# Changelog

All notable changes to **EziTour** will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [0.1.0] - 2026-07-25

Initial release of EziTour — Indonesian travel & tour package booking platform.

### Added

- **Initial release** of EziTour with the brand voice *"Liburan impian tanpa ribet — pilih, pesan, nikmati."*
- **Public catalog**: landing page, browsable package list with search & pagination, and package detail pages resolved by slug.
- **Checkout flow**: inline guest login/register during checkout, real-time price calculation, and booking creation via `CheckoutSessionService` + `BookingCreationService`.
- **Payment integration**: Midtrans Snap integration with create/retry/finish/unfinish/error callbacks plus a webhook endpoint (`POST /midtrans/notification`) guarded by rate-limited middleware (`webhook.ratelimit`).
- **User dashboard**: booking history with paid/unpaid filter pill tabs, badge counts, and per-filter empty states.
- **Admin panel**: Filament v3 panel at `/admin` with six resources — Booking, Car, Destination, Driver, Package, and Transaction.
- **Authentication**: login (with pending-booking resume) and register screens with a premium split layout, branding panel, form card with `border-2` icon inputs, trust indicators, and a password-toggle helper (vanilla JS).
- **Brand theme system "Ocean & Sand"**: custom ocean-blue scale (overriding Tailwind's `blue-*`), sand-amber accent, **Plus Jakarta Sans** display font paired with **Instrument Sans** body (Bunny Font CDN), radius tokens (card/input/button/pill), and shadow tokens (soft/card/hover with brand glow) defined via Tailwind v4 `@theme` in `resources/css/app.css`.
- **Landing page sections (7)**: hero with gradient mesh + inline SVG, stats bar, USP grid, featured packages, how-it-works, testimonials with initial-circle avatars, and CTA banner.
- **Packages list/show**: tokenized layout with hover lift, itinerary timeline with gradient step badges, and a sticky booking card.
- **Payment page**: gradient header and info checklist, with the Midtrans Snap trigger (vanilla JS) preserved.
- **Webhook rate limiting** middleware to protect the Midtrans notification endpoint.
- **Midtrans configuration**: dedicated `config/midtrans.php` and required environment variables (`MIDTRANS_SERVER_KEY`, `MIDTRANS_CLIENT_KEY`, `MIDTRANS_IS_PRODUCTION`, `MIDTRANS_IS_SANITIZED`, `MIDTRANS_IS_3DS`, `MIDTRANS_EXPIRY_DURATION`).
- **Project metadata**: `README.md`, `CHANGELOG.md`, and `composer.json` branding/metadata for the first release.

### Changed

- **Laravel Boost upgraded from v1 to v2** (`laravel/boost` `^1.8` → `^2.0`); `boost.json` migrated to the v2 format and `boost:update` added to the Composer `post-update-cmd` script.
- **`composer.json` metadata** updated from the default Laravel skeleton (`laravel/laravel`) to `ryansutrisno/ezitour` with project-specific description, `version` (`0.1.0`), and keyword set (`laravel`, `travel`, `tour`, `booking`, `midtrans`, `filament`, `indonesia`).
- **Dashboard styling** refined with polished filter pill tabs, booking cards featuring a status badge, hover shadow, and thumbnail fallback.
- **Authentication screens** redesigned with a premium split layout (branding panel + form card) and trust indicators, keeping the password-toggle and Midtrans Snap trigger JavaScript intact.

[0.1.0]: https://github.com/ryansutrisno/ezitour/releases/tag/v0.1.0
