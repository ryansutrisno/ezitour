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

===

<laravel-boost-guidelines>
=== foundation rules ===

# Laravel Boost Guidelines

The Laravel Boost guidelines are specifically curated by Laravel maintainers for this application. These guidelines should be followed closely to ensure the best experience when building Laravel applications.

## Foundational Context

This application is a Laravel application and its main Laravel ecosystems package & versions are below. You are an expert with them all. Ensure you abide by these specific packages & versions.

- php - 8.4
- filament/filament (FILAMENT) - v3
- laravel/framework (LARAVEL) - v12
- laravel/prompts (PROMPTS) - v0
- livewire/livewire (LIVEWIRE) - v3
- laravel/boost (BOOST) - v2
- laravel/mcp (MCP) - v0
- laravel/pail (PAIL) - v1
- laravel/pint (PINT) - v1
- laravel/sail (SAIL) - v1
- phpunit/phpunit (PHPUNIT) - v11
- tailwindcss (TAILWINDCSS) - v4

## Skills Activation

This project has domain-specific skills available in `**/skills/**`. You MUST activate the relevant skill whenever you work in that domain—don't wait until you're stuck.

## Conventions

- You must follow all existing code conventions used in this application. When creating or editing a file, check sibling files for the correct structure, approach, and naming.
- Use descriptive names for variables and methods. For example, `isRegisteredForDiscounts`, not `discount()`.
- Check for existing components to reuse before writing a new one.

## Verification Scripts

- Do not create verification scripts or tinker when tests cover that functionality and prove they work. Unit and feature tests are more important.

## Application Structure & Architecture

- Stick to existing directory structure; don't create new base folders without approval.
- Do not change the application's dependencies without approval.

## Frontend Bundling

- If the user doesn't see a frontend change reflected in the UI, it could mean they need to run `npm run build`, `npm run dev`, or `composer run dev`. Ask them.

## Documentation Files

- You must only create documentation files if explicitly requested by the user.

## Replies

- Be concise in your explanations - focus on what's important rather than explaining obvious details.

=== boost rules ===

# Laravel Boost

## Tools

- Laravel Boost is an MCP server with tools designed specifically for this application. Prefer Boost tools over manual alternatives like shell commands or file reads.
- Use `database-query` to run read-only queries against the database instead of writing raw SQL in tinker.
- Use `database-schema` to inspect table structure before writing migrations or models.
- Use `get-absolute-url` to resolve the correct scheme, domain, and port for project URLs. Always use this before sharing a URL with the user.
- Use `browser-logs` to read browser logs, errors, and exceptions. Only recent logs are useful, ignore old entries.

## Searching Documentation (IMPORTANT)

- Always use `search-docs` before making code changes. Do not skip this step. It returns version-specific docs based on installed packages automatically.
- Pass a `packages` array to scope results when you know which packages are relevant.
- Use multiple broad, topic-based queries: `['rate limiting', 'routing rate limiting', 'routing']`. Expect the most relevant results first.
- Do not add package names to queries because package info is already shared. Use `test resource table`, not `filament 4 test resource table`.

### Search Syntax

1. Use words for auto-stemmed AND logic: `rate limit` matches both "rate" AND "limit".
2. Use `"quoted phrases"` for exact position matching: `"infinite scroll"` requires adjacent words in order.
3. Combine words and phrases for mixed queries: `middleware "rate limit"`.
4. Use multiple queries for OR logic: `queries=["authentication", "middleware"]`.

## Artisan

- Run Artisan commands directly via the command line (e.g., `php artisan route:list`). Use `php artisan list` to discover available commands and `php artisan [command] --help` to check parameters.
- Inspect routes with `php artisan route:list`. Filter with: `--method=GET`, `--name=users`, `--path=api`, `--except-vendor`, `--only-vendor`.
- Read configuration values using dot notation: `php artisan config:show app.name`, `php artisan config:show database.default`. Or read config files directly from the `config/` directory.

## Tinker

- Execute PHP in app context for debugging and testing code. Do not create models without user approval, prefer tests with factories instead. Prefer existing Artisan commands over custom tinker code.
- Always use single quotes to prevent shell expansion: `php artisan tinker --execute 'Your::code();'`
  - Double quotes for PHP strings inside: `php artisan tinker --execute 'User::where("active", true)->count();'`

=== php rules ===

# PHP

- Always use curly braces for control structures, even for single-line bodies.
- Use PHP 8 constructor property promotion: `public function __construct(public GitHub $github) { }`. Do not leave empty zero-parameter `__construct()` methods unless the constructor is private.
- Use explicit return type declarations and type hints for all method parameters: `function isAccessible(User $user, ?string $path = null): bool`
- Use TitleCase for Enum keys: `FavoritePerson`, `BestLake`, `Monthly`.
- Prefer PHPDoc blocks over inline comments. Only add inline comments for exceptionally complex logic.
- Use array shape type definitions in PHPDoc blocks.

=== deployments rules ===

# Deployment

- Laravel can be deployed using [Laravel Cloud](https://cloud.laravel.com/), which is the fastest way to deploy and scale production Laravel applications.

=== herd rules ===

# Laravel Herd

- The application is served by Laravel Herd at `https?://[kebab-case-project-dir].test`. Use the `get-absolute-url` tool to generate valid URLs. Never run commands to serve the site. It is always available.
- Use the `herd` CLI to manage services, PHP versions, and sites (e.g. `herd sites`, `herd services:start <service>`, `herd php:list`). Run `herd list` to discover all available commands.

=== tests rules ===

# Test Enforcement

- Every change must be programmatically tested. Write a new test or update an existing test, then run the affected tests to make sure they pass.
- Run the minimum number of tests needed to ensure code quality and speed. Use `php artisan test --compact` with a specific filename or filter.

=== laravel/core rules ===

# Do Things the Laravel Way

- Use `php artisan make:` commands to create new files (i.e. migrations, controllers, models, etc.). You can list available Artisan commands using `php artisan list` and check their parameters with `php artisan [command] --help`.
- If you're creating a generic PHP class, use `php artisan make:class`.
- Pass `--no-interaction` to all Artisan commands to ensure they work without user input. You should also pass the correct `--options` to ensure correct behavior.

### Model Creation

- When creating new models, create useful factories and seeders for them too. Ask the user if they need any other things, using `php artisan make:model --help` to check the available options.

## APIs & Eloquent Resources

- For APIs, default to using Eloquent API Resources and API versioning unless existing API routes do not, then you should follow existing application convention.

## URL Generation

- When generating links to other pages, prefer named routes and the `route()` function.

## Testing

- When creating models for tests, use the factories for the models. Check if the factory has custom states that can be used before manually setting up the model.
- Faker: Use methods such as `$this->faker->word()` or `fake()->randomDigit()`. Follow existing conventions whether to use `$this->faker` or `fake()`.
- When creating tests, make use of `php artisan make:test [options] {name}` to create a feature test, and pass `--unit` to create a unit test. Most tests should be feature tests.

## Vite Error

- If you receive an "Illuminate\Foundation\ViteException: Unable to locate file in Vite manifest" error, you can run `npm run build` or ask the user to run `npm run dev` or `composer run dev`.

=== laravel/v12 rules ===

# Laravel 12

- CRITICAL: ALWAYS use `search-docs` tool for version-specific Laravel documentation and updated code examples.
- Since Laravel 11, Laravel has a new streamlined file structure which this project uses.

## Laravel 12 Structure

- In Laravel 12, middleware are no longer registered in `app/Http/Kernel.php`.
- Middleware are configured declaratively in `bootstrap/app.php` using `Application::configure()->withMiddleware()`.
- `bootstrap/app.php` is the file to register middleware, exceptions, and routing files.
- `bootstrap/providers.php` contains application specific service providers.
- The `app/Console/Kernel.php` file no longer exists; use `bootstrap/app.php` or `routes/console.php` for console configuration.
- Console commands in `app/Console/Commands/` are automatically available and do not require manual registration.

## Database

- When modifying a column, the migration must include all of the attributes that were previously defined on the column. Otherwise, they will be dropped and lost.
- Laravel 12 allows limiting eagerly loaded records natively, without external packages: `$query->latest()->limit(10);`.

### Models

- Casts can and likely should be set in a `casts()` method on a model rather than the `$casts` property. Follow existing conventions from other models.

=== pint/core rules ===

# Laravel Pint Code Formatter

- If you have modified any PHP files, you must run `vendor/bin/pint --dirty` before finalizing changes to ensure your code matches the project's expected style.
- Do not run `vendor/bin/pint --test`, simply run `vendor/bin/pint` to fix any formatting issues.

=== phpunit/core rules ===

# PHPUnit

- This application uses PHPUnit for testing. All tests must be written as PHPUnit classes. Use `php artisan make:test --phpunit {name}` to create a new test.
- If you see a test using "Pest", convert it to PHPUnit.
- Every time a test has been updated, run that singular test.
- When the tests relating to your feature are passing, ask the user if they would like to also run the entire test suite to make sure everything is still passing.
- Tests should cover all happy paths, failure paths, and edge cases.
- You must not remove any tests or test files from the tests directory without approval. These are not temporary or helper files; these are core to the application.

## Running Tests

- Run the minimal number of tests, using an appropriate filter, before finalizing.
- To run all tests: `php artisan test --compact`.
- To run all tests in a file: `php artisan test --compact tests/Feature/ExampleTest.php`.
- To filter on a particular test name: `php artisan test --compact --filter=testName` (recommended after making a change to a related file).

</laravel-boost-guidelines>
