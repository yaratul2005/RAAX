# RAAX ERP Base Core

RAAX ERP is an enterprise-grade ERP system built on Laravel 12 using a Domain-Driven Modular Monolith architecture.

## Architecture & Configuration

* **Modular Structure:** The application code is separated into business modules located in the root `/modules/` directory. Currently implemented modules include:
  * `Finance`: Double-entry accounting, general ledger, chart of accounts, trial balance, and Bangladesh VAT logic.
  * `HR`: Organizational management, employee directory, shift configurations, daily attendance tracking, and grace period execution.
* **Tenant Isolation:** Row-Level Security (RLS) policies are active at the PostgreSQL database level for isolation.
* **Role Requirements:** To utilize RLS, connect to the database via the `app_user` role, which should be configured with `NOBYPASSRLS` and `NOSUPERUSER` flags.

## Local Environment Setup

If you are cloning this repository for the first time, take the following steps to configure your environment for tests and local development:

1. Copy `.env.example` to `.env` and generate an app key:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
2. Configure your database connections. For production/staging, PostgreSQL must be used to enforce RLS properly. Set up the `app_user` role using the script found in `database/scripts/rls_setup.sql`.
   *Note: For simple testing, SQLite is supported but automatically bypasses the RLS commands in the codebase.*
3. Install dependencies:
   ```bash
   composer install
   npm install && npm run build
   ```
4. Run migrations:
   ```bash
   php artisan migrate
   ```

## Development Commands
- Code Formatting: `vendor/bin/pint`
- Static Analysis: `vendor/bin/phpstan analyse --level=8`
- Testing: `php artisan test` or `vendor/bin/phpunit`
