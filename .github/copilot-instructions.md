# RAAX ERP - Global Engineering Rules & Guidelines

## 1. App Summary

RAAX is a high-performance, multi-tenant ERP platform designed for transnational holding operations. It manages general ledger accounts, automated progressive payroll taxes, FIFO inventory, JIT manufacturing, asset depreciation, and NBR regulatory compliance.

## 2. Technology Stack & Versions

* Backend: PHP 8.3 / Laravel 12 (Stateless Modular Monolith API)
* Database: PostgreSQL 16 (Multi-Tenant Row-Level Security)
* Caching & Queues: Redis 7.2 / Laravel Horizon
* Monitoring & Telemetry: Laravel Pulse

## 3. Global DevOps & Git Hygiene Rules

* Zero-Downtime Database Migrations: Use the expand-and-contract pattern. Never drop or rename columns directly.
* Git Tracking Prevention: Never stage, commit, or track framework-generated or dependency directories (including `/vendor`, `/node_modules`, `/.phpunit.cache`, `/public/build`, and `.env`).
* Absolute Determinism: Under no circumstances should you generate or integrate client-side predictive ML models or natural language LLM wrappers.

## 4. Unified Folder Structure

Modules are strictly encapsulated under the root `/modules` directory:

```
modules/
├── Finance/       # Double-entry ledger, MT940 parsing, and tax localization
├── HR/            # Employees directory, shift rosters, and tax payroll
├── Inventory/     # Bins tracking, FIFO batches, and stock transfers
├── Manufacturing/ # Bill of Materials and JIT MRP engine
├── Sales/         # Customer profiles and credit exposure controls
└── EDI/           # B2B ANSI X12 API gateway and rate limiters
```

## 5. Development Tools & CLI Commands

* Linting & Code Style: Run `vendor/bin/pint --test`
* Static Code Analysis: Run `vendor/bin/phpstan analyse --level=8`
* Testing: Execute parallel tests with `php artisan test --parallel`
