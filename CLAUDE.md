# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

Ezitour is a Laravel 12 tour booking application with payment integration via Midtrans. Features include:

- Tour package browsing and booking
- Filament-based admin panel for managing bookings, packages, transactions
- Midtrans payment gateway integration (Snap, notifications)
- Multi-step checkout with guest/authenticated user support

## Laravel Boost Guidelines

Project ini menggunakan Laravel Boost (terinstall via `php artisan boost:install`). Boost menyediakan:

- **MCP servers** untuk Claude Code, Codex, OpenCode, VS Code
- **AI guidelines** untuk Laravel v12, Filament v3, Livewire v3, Tailwind v4
- **Tools**: `search-docs`, `tinker`, `database-query`, `browser-logs`

> **Important:** Gunakan `search-docs` sebelum membuat perubahan code untuk Laravel ecosystem packages. Boost akan mengembalikan dokumentasi version-specific untuk packages yang terinstall.

## Architecture

### Directory Structure

```
app/
├── Filament/Resources/          # Admin panel resources
│   ├── BookingResource.php
│   ├── TransactionResource.php
│   ├── PackageResource.php
│   ├── CarResource.php
│   ├── DestinationResource.php
│   └── DriverResource.php
├── Http/Controllers/
│   ├── Front/                   # Frontend controllers
│   │   ├── HomeController.php
│   │   ├── PackageController.php
│   │   ├── CheckoutController.php
│   │   ├── PaymentController.php
│   │   └── MidtransNotificationController.php
│   └── Auth/                    # Auth controllers
├── Models/                       # Eloquent models
│   ├── Booking.php
│   ├── Package.php
│   ├── Transaction.php
│   ├── User.php
│   ├── Car.php
│   ├── Destination.php
│   ├── Driver.php
│   └── PackageItem.php
├── Services/                     # Business logic
│   ├── PaymentService.php        # Core payment processing
│   ├── MidtransClient.php        # Midtrans API wrapper
│   ├── PaymentErrorHandler.php   # Error handling
│   ├── PaymentLogger.php         # Payment audit logging
│   ├── CheckoutSessionService.php
│   └── BookingCreationService.php
└── Exceptions/                   # Custom exceptions
    ├── MidtransApiException.php
    ├── PaymentException.php
    └── NotificationProcessingException.php
```

### Key Technologies

- **Laravel 12** with PHP 8.2+
- **Filament 3** for admin panel
- **Midtrans** payment gateway (via midtrans/midtrans-php)
- **Tailwind CSS v4** with Vite
- **MySQL** database

### Payment Flow

1. User selects package → `PackageController@show`
2. Checkout page → `CheckoutController@show/store`
3. Payment creation → `PaymentService::createPayment()`
4. Midtrans Snap token generated
5. Frontend shows Midtrans Snap popup
6. Payment notification → `MidtransNotificationController@handle`
7. Transaction status updated via `PaymentService::processNotification()`

## Common Commands

### Setup

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan db:seed        # if seeders exist
```

### Development

```bash
# Start dev server with hot reload
npm run dev

# Start Laravel dev server
php artisan serve

# Queue worker (for payments)
php artisan queue:work

# All at once (using composer dev script)
composer dev
```

### Testing

```bash
# Run all tests
php artisan test

# Run specific test file
php artisan test tests/Feature/BookingTest.php
php artisan test tests/Feature/PaymentTest.php

# Run with filter (specific test method)
php artisan test --filter=test_user_can_create_booking

# Run with coverage (requires Xdebug)
php artisan test --coverage

# PHPUnit directly
./vendor/bin/phpunit
```

### Code Quality

```bash
# PHP CS Fixer (Laravel Pint)
./vendor/bin/pint

# Run on specific file
./vendor/bin/pint app/Services/PaymentService.php
```

### Database

```bash
# Fresh migrate with seed
php artisan migrate:fresh --seed

# Rollback last batch
php artisan migrate:rollback

# Check status
php artisan migrate:status

# Create migration
php artisan make:migration add_column_to_table
```

### Filament (Admin)

```bash
# Create resource
php artisan make:filament-resource Booking

# Create widget
php artisan make:filament-widget BookingStats

# Upgrade Filament
php artisan filament:upgrade
```

### Key Artisan Commands

```bash
# Clear caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan event:clear

# Rebuild caches (production)
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Queue
php artisan queue:work
php artisan queue:restart

# Tinker (interactive shell)
php artisan tinker

# Maintenance mode
php artisan down
php artisan up
```

## Testing Guide

### Test Organization

```
tests/
├── Feature/           # HTTP/Feature tests
│   ├── BookingTest.php
│   └── PaymentTest.php
├── Unit/              # Unit tests
│   ├── Services/
│   └── Models/
└── TestCase.php
```

### Running Tests with Specific Configurations

```bash
# Run tests with coverage (requires XDebug)
php artisan test --coverage

# Run with memory limit
php -d memory_limit=512M artisan test

# Run specific test method
php artisan test --filter=test_user_can_create_booking
```

## Filament Admin Resources

Resources located in `app/Filament/Resources/`:

- **BookingResource**: Manage tour bookings
- **TransactionResource**: View payment transactions
- **PackageResource**: CRUD tour packages
- **CarResource**: Manage vehicles
- **DestinationResource**: Tour destinations
- **DriverResource**: Driver assignments

## Payment Integration (Midtrans)

Key files:
- `app/Services/PaymentService.php` - Core payment logic
- `app/Services/MidtransClient.php` - API client
- `app/Http/Controllers/Front/MidtransNotificationController.php` - Webhook handler
- `docs/PAYMENT_DEPLOYMENT_CHECKLIST.md` - Production deployment guide

Required environment variables:
```env
MIDTRANS_SERVER_KEY=Mid-server-xxx
MIDTRANS_CLIENT_KEY=Mid-client-xxx
MIDTRANS_IS_PRODUCTION=true
```

## Code Quality Standards

### PHP Standards
- Gunakan **constructor property promotion** (PHP 8)
- Selalu gunakan **explicit return type declarations**
- Gunakan curly braces untuk control structures, meski satu line
- Gunakan descriptive names: `isRegisteredForDiscounts`, bukan `discount()`

### Formatting
- Jalankan Laravel Pint sebelum commit:
  ```bash
  ./vendor/bin/pint
  ./vendor/bin/pint app/Services/PaymentService.php  # specific file
  ```

## Troubleshooting

### Common Issues

**Permission denied on storage:**
```bash
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

**Class not found errors:**
```bash
composer dump-autoload
```

**Config cache issues:**
```bash
php artisan config:clear
php artisan cache:clear
```

**Database connection refused:**
- Check `.env` DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME, DB_PASSWORD
- Ensure MySQL/MariaDB is running
- For local: `DB_HOST=127.0.0.1` not `localhost`

**Vite/HMR issues:**
```bash
rm -rf node_modules
npm install
npm run dev
```

**Queue worker not processing jobs:**
```bash
php artisan queue:restart
php artisan queue:work --verbose
```

### Payment Testing

For testing Midtrans payments in sandbox:
- Use Midtrans [test cards](https://docs.midtrans.com/docs/testing-2)
- Snap popup will appear during checkout flow
- Check `storage/logs/payments-*.log` for payment audit trails
