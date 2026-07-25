## [1.4.0](https://github.com/ryansutrisno/ezitour/compare/v1.3.0...v1.4.0) (2026-07-25)


### ✨ Features

* add user profile management with avatar upload ([3826789](https://github.com/ryansutrisno/ezitour/commit/3826789da7c778d5b1ce5f43aa2210fa2c3bab63))

## [1.3.0](https://github.com/ryansutrisno/ezitour/compare/v1.2.0...v1.3.0) (2026-07-25)


### ✨ Features

* add Testimonial + FAQ CRUD and footer social icons ([a8ef7c6](https://github.com/ryansutrisno/ezitour/commit/a8ef7c6464e6bdd759a7e7b01521f1cbb64f09d6))


### 🔧 Chores

* update app branding and contact details ([ae5e23b](https://github.com/ryansutrisno/ezitour/commit/ae5e23b6faf779009e347672ef37ca30bccf2f03))
* update support email to hallo@trazmedia.com ([2b48241](https://github.com/ryansutrisno/ezitour/commit/2b48241f9bc7c99edd864e1f97ee9609cbd6bca5))

## [1.2.0](https://github.com/ryansutrisno/ezitour/compare/v1.1.0...v1.2.0) (2026-07-25)


### ✨ Features

* add spatie/laravel-settings integration with Filament admin pages ([a715d00](https://github.com/ryansutrisno/ezitour/commit/a715d00fc76b400ea89d5634d618eb63c7c0015e))

## [1.1.0](https://github.com/ryansutrisno/ezitour/compare/v1.0.0...v1.1.0) (2026-07-25)


### ✨ Features

* add about page and navigation enhancements ([f8c3ba5](https://github.com/ryansutrisno/ezitour/commit/f8c3ba5bd4e9c02decef4571e7e7b78786826b1f))

## 1.0.0 (2026-07-25)


### 🐛 Bug Fixes

* update payment routes to POST and add CI/CD ([3e8396c](https://github.com/ryansutrisno/ezitour/commit/3e8396c8bbe4ef70764b6294b40440bbf65ff6f4))


### ♻️ Refactoring

* clean up code, remove unused imports and update config ([6dc7ecd](https://github.com/ryansutrisno/ezitour/commit/6dc7ecdc7b0a5bcc57415427c17f281f82ea7a8b))


### 👷 CI/CD

* remove the unused linter GitHub workflow ([b0adcfc](https://github.com/ryansutrisno/ezitour/commit/b0adcfcb2ffc6d5219d4ac011369866934e403b3))

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
