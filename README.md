<div align="center">

# 🌊 EziTour

**Liburan impian tanpa ribet — pilih, pesan, nikmati.**

Indonesian travel & tour package booking platform built with Laravel 12, Filament v3, Blade, Tailwind CSS v4, and Midtrans.

</div>

---

EziTour is an end-to-end tour package booking application for the Indonesian market.
Travelers browse curated tour packages, check out with real-time price calculation,
pay through Midtrans Snap, and track their bookings from a personal dashboard — while
operators manage packages, destinations, cars, drivers, bookings, and transactions from
a Filament v3 admin panel. The frontend is intentionally lightweight (Blade + vanilla JS)
and styled with a custom **"Ocean & Sand"** design system built on Tailwind v4 `@theme` tokens.

## ✨ Features

**Public catalog**
- Landing page with hero, stats, USPs, featured packages, how-it-works, testimonials, and CTA
- Browsable package list with search and pagination
- Package detail pages resolved by slug, with itinerary timeline and sticky booking card

**Checkout**
- Inline guest login/register during checkout (no separate auth wall)
- Real-time price calculation as guests configure their booking
- Booking creation through `CheckoutSessionService` + `BookingCreationService`

**Payment**
- Midtrans Snap integration with create / retry / finish / unfinish / error callbacks
- Secure webhook endpoint (`POST /midtrans/notification`) protected by rate-limited middleware (`webhook.ratelimit`)

**Dashboard**
- Personal booking history with **paid** / **unpaid** filter pill tabs
- Badge counts per filter and dedicated empty states

**Admin**
- Filament v3 panel at `/admin`
- Six resources: **Booking**, **Car**, **Destination**, **Driver**, **Package**, **Transaction**

**Auth**
- Login (with pending-booking resume) and register
- Premium split layout with branding panel and trust indicators

## 🧰 Tech Stack

| Layer            | Technology                                                                            |
| ---------------- | ------------------------------------------------------------------------------------- |
| Language         | PHP 8.4                                                                               |
| Framework        | Laravel 12                                                                            |
| Admin panel      | Filament v3 (Livewire v3 transitive)                                                  |
| Frontend         | Blade + vanilla JS (no Alpine, no Livewire components, no React/Inertia)              |
| Styling          | Tailwind CSS v4 (CSS-first via `@theme`)                                              |
| Assets           | Laravel Vite                                                                          |
| Fonts            | Bunny Font CDN — *Instrument Sans* (body) + *Plus Jakarta Sans* (display, Tokotype)  |
| Payments         | Midtrans Snap (`midtrans/midtrans-php` ^2.6)                                          |
| Testing          | PHPUnit 11                                                                            |
| Tooling          | Laravel Boost v2, Laravel Pint, Laravel Sail, Laravel Pail, Laravel MCP              |

## 📋 Requirements

- **PHP** 8.4+
- **Composer** 2+
- **Node.js** 18+ (with npm)
- A database: **MySQL**, **PostgreSQL**, or **SQLite**
- A **Midtrans** account (sandbox or production) — required only for payment features

## 🚀 Installation

The fastest path is the bundled setup script (already configured in `composer.json`):

```bash
composer run setup
```

This runs `composer install`, copies `.env.example` → `.env`, generates the app key,
runs migrations, and builds frontend assets with npm.

### Manual steps

If you prefer to run each step yourself:

```bash
# 1. Install PHP dependencies
composer install

# 2. Environment
cp .env.example .env
php artisan key:generate

# 3. Configure your DB_* credentials in .env, then migrate & seed
php artisan migrate --force
php artisan db:seed

# 4. Frontend dependencies & build
npm install
npm run build
```

## 💻 Development

Run the full local dev environment (server + queue + Pail logs + Vite) concurrently:

```bash
composer dev
```

Or run each process standalone:

```bash
php artisan serve      # Laravel dev server
npm run dev            # Vite HMR
php artisan pail       # Tail logs
```

> On Laravel Herd, the site is already served at `https://ezitour.test` — no `php artisan serve` needed.

## 🧪 Testing

```bash
composer test
# or
php artisan test --compact
```

## 👨‍💼 Admin Access

The admin panel is provided by Filament v3 and is served at:

```
/admin
```

The `DatabaseSeeder` creates a default admin user you can use to sign in:

| Field    | Value              |
| -------- | ------------------ |
| Email    | `admin@ezitour.com` |
| Password | `password`         |

> Rotate this credential immediately in any non-local environment.

## 💳 Midtrans Setup

Payments require a Midtrans account. Configure the following in your `.env`:

```env
MIDTRANS_SERVER_KEY=        # Server Key from Midtrans Dashboard (SECRET)
MIDTRANS_CLIENT_KEY=        # Client Key used by Snap.js on the frontend
MIDTRANS_IS_PRODUCTION=false # false = sandbox, true = production
MIDTRANS_IS_SANITIZED=true   # recommended
MIDTRANS_IS_3DS=true         # recommended
MIDTRANS_EXPIRY_DURATION=1440 # payment expiry in minutes (default 24h)
```

- Use **sandbox** keys (prefix `SB-Mid-...`) during development, then switch `MIDTRANS_IS_PRODUCTION=true` and use production keys (`Mid-...`) for go-live.
- Configure the following webhook URL in the Midtrans Dashboard:

  ```
  {APP_URL}/midtrans/notification
  ```

  where `APP_URL` matches your `APP_URL` env value (e.g. `https://ezitour.test/midtrans/notification`).

## 📁 Project Structure

```
ezitour/
├── app/
│   ├── Filament/Resources/        # Booking, Car, Destination, Driver, Package, Transaction
│   ├── Http/Controllers/
│   │   ├── Auth/                  # LoginController, RegisterController
│   │   └── Front/                 # Home, Package, Checkout, Booking, Payment, Dashboard, MidtransNotification
│   ├── Models/                    # User, Package, PackageItem, Booking, Transaction, Destination, Car, Driver
│   └── Services/                  # CheckoutSessionService, BookingCreationService, PaymentService, MidtransClient, ...
├── bootstrap/app.php              # Middleware, exceptions, routing (Laravel 12 streamlined structure)
├── config/midtrans.php            # Midtrans credentials & behaviour
├── database/
│   ├── migrations/
│   └── seeders/                   # DatabaseSeeder, DestinationSeeder, PackageSeeder
├── resources/
│   ├── css/app.css                # Tailwind v4 @theme tokens ("Ocean & Sand" design system)
│   └── views/
│       ├── layouts/front.blade.php # Global front-end layout
│       ├── front/                 # home, packages, checkout, dashboard, payments
│       └── auth/                  # login, register
├── routes/web.php                 # Public, checkout, auth, payment, webhook routes
└── composer.json
```

## 🎨 Design System

EziTour ships with a custom **"Ocean & Sand"** brand theme, defined CSS-first via Tailwind v4
`@theme` tokens in [`resources/css/app.css`](resources/css/app.css):

- **Ocean blue palette** — overrides Tailwind's default `blue-*` scale with a brand-tuned ocean blue
- **Sand amber accent** — warm secondary color
- **Typography** — *Plus Jakarta Sans* (display, by Tokotype) over *Instrument Sans* (body), served via the Bunny Font CDN
- **Radius tokens** — `card`, `input`, `button`, `pill`
- **Shadow tokens** — `soft`, `card`, `hover` (with brand glow)

The global front-end chrome lives in [`resources/views/layouts/front.blade.php`](resources/views/layouts/front.blade.php).

## 🚢 Deployment

EziTour is a standard Laravel 12 application and can be deployed anywhere Laravel runs.

- **Laravel Herd** (local/macOS) — the app is served automatically at `https://ezitour.test`.
- **Laravel Cloud** — the fastest way to deploy and scale production Laravel apps.
- **Standard VPS** — typical deploy steps:

  ```bash
  composer install --no-dev --optimize-autoloader
  npm install && npm run build
  php artisan migrate --force
  php artisan config:cache
  php artisan route:cache
  php artisan view:cache
  ```

Remember to set production Midtrans keys and `MIDTRANS_IS_PRODUCTION=true`, then register the
webhook URL `{APP_URL}/midtrans/notification` in your Midtrans Dashboard.

### ⏰ Scheduled Tasks

EziTour registers two hourly reminder jobs via Laravel Task Scheduling:

- **`reminders:trip`** — kirim pengingat H-1 ke booking berstatus *paid* yang
  travel_date-nya jatuh besok (closed-loop post-booking engagement).
- **`reminders:payment-expiry`** — kirim pengingat ke booking *pending* yang
  mendekati jendela kedaluwarsa pembayaran (±4 jam sebelum `created_at + expiry_duration`).

Both jobs are idempotent (gated by `trip_reminder_sent_at` / `payment_reminder_sent_at`
columns on `bookings`) so overlapping scheduler ticks never double-send.

Add the standard Laravel cron entry on your production server so the scheduler runs every minute:

```bash
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

On Laravel Cloud the scheduler is configured automatically — no cron entry required.

## 📝 Changelog

See [CHANGELOG.md](CHANGELOG.md) for release history. This project follows
[Keep a Changelog](https://keepachangelog.com/en/1.1.0/) and
[Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## 📄 License

EziTour is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
