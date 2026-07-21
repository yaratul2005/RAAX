# RAAX ERP - Enterprise Modular Monolith

![Architecture](https://img.shields.io/badge/Architecture-Modular_Monolith-0F766E?style=for-the-badge)
![Tech Stack](https://img.shields.io/badge/Stack-PHP_8.3_|_Laravel_12-10B981?style=for-the-badge)
![Security](https://img.shields.io/badge/Security-PostgreSQL_RLS-F59E0B?style=for-the-badge)
![Repository](https://img.shields.io/badge/Git_Tree-<1.5_MB_Clean-3B82F6?style=for-the-badge)

<p align="center">
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 800 300" fill="none" width="100%">
  <rect width="800" height="300" rx="8" fill="#0F172A"/>
  <path d="M 0 30 L 800 30 M 0 60 L 800 60" stroke="#1E293B" stroke-width="0.5"/>
  <text x="400" y="45" font-family="monospace" font-size="14" fill="#0F766E" text-anchor="middle" font-weight="bold">RAAX CORE ARCHITECTURE BLUEPRINT</text>
  <rect x="50" y="100" width="180" height="80" rx="4" fill="#1E293B" stroke="#0F766E" stroke-width="1.5"/>
  <text x="140" y="145" font-family="monospace" font-size="14" fill="#F8FAFC" text-anchor="middle">modules/Finance</text>
  <rect x="310" y="100" width="180" height="80" rx="4" fill="#1E293B" stroke="#0F766E" stroke-width="1.5"/>
  <text x="400" y="145" font-family="monospace" font-size="14" fill="#F8FAFC" text-anchor="middle">modules/HR</text>
  <rect x="570" y="100" width="180" height="80" rx="4" fill="#1E293B" stroke="#0F766E" stroke-width="1.5"/>
  <text x="660" y="145" font-family="monospace" font-size="14" fill="#F8FAFC" text-anchor="middle">modules/Inventory</text>
  <rect x="250" y="220" width="300" height="50" rx="4" fill="#0F766E" fill-opacity="0.1" stroke="#10B981" stroke-width="1.5" stroke-dasharray="4,4"/>
  <text x="400" y="250" font-family="monospace" font-size="13" fill="#10B981" text-anchor="middle">PostgreSQL 16 Database (RLS Enforced)</text>
</svg>
</p>

## Overview

RAAX ERP is an enterprise-grade resource planning system architected as a Domain-Driven Modular Monolith. It guarantees mathematical precision through integer-based financial calculations and enforces strict multi-tenant data isolation directly at the PostgreSQL connection layer using Row-Level Security (RLS).

## Core Principles

- **Zero Floating-Point Drift:** All money, tax rates, inventory values, and basis points are strictly calculated using integer arithmetic.
- **Deep Tenant Isolation:** Bypassing standard ORM global scopes, tenant security is enforced at the database level using session variables tied to the `app_user` role.
- **Strict Decoupling:** Cross-module boundaries communicate through registered interfaces and asynchronous Redis events (e.g., `SalaryPaymentApproved`, `IntercompanyTransferCompleted`).

---

## Local Setup & Installation

Follow these explicit steps to spin up the local development environment securely:

### 1. Install Dependencies
```bash
composer install
cp .env.example .env
php artisan key:generate
```

### 2. Configure PostgreSQL App Role
To enforce RLS, you must create a restricted database role that Laravel will use to connect. Do **not** run the application as the Postgres superuser.
```sql
-- Connect as superuser (postgres)
CREATE ROLE app_user WITH LOGIN NOBYPASSRLS NOSUPERUSER PASSWORD 'your_secure_password';
GRANT ALL PRIVILEGES ON DATABASE your_database_name TO app_user;
```

Update your `.env`:
```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=your_database_name
DB_USERNAME=app_user
DB_PASSWORD=your_secure_password
```

### 3. Run Migrations & Setup Horizon
```bash
php artisan migrate
php artisan horizon:install
```

### 4. Execute Isolated Test Suite
```bash
vendor/bin/pint --test
vendor/bin/phpstan analyse --level=8
vendor/bin/phpunit
```

---

## Milestone Tracking Matrix

| Phase | Milestone | Domain | Status | Description |
|---|---|---|---|---|
| **Phase 1** | 1-5 | Core & HR | ✅ Completed | Tenant Context Middleware, Chart of Accounts, Double-Entry Posting, Shift Registries, and Grace Period Attendance. |
| **Phase 1** | 6 | Security | ✅ Completed | OIDC Authentication & Role-Based Access Control (RBAC) Engine. |
| **Phase 1** | 7 | Events | ✅ Completed | Redis-Backed Queue & Laravel Horizon Notification Dispatcher. |
| **Phase 1** | 8 | Finance | ✅ Completed | AP/AR Invoice Aging Analytics and Cash Flow Projection. |
| **Phase 1** | 9 | HR/Finance | ✅ Completed | NBR Withholding Tax (AY 2026-27 Slabs) & Automated Payslip Journals. |
| **Phase 2** | 10 | Procurement | ✅ Completed | Vendor Registries & Multi-Tier Purchase Order Approval Limits. |
| **Phase 2** | 11 | Inventory | ✅ Completed | Multi-Warehouse Bins, Goods Received Notes, & FIFO Costing Depletion. |
| **Phase 2** | 12 | Sales | ✅ Completed | Order Confirmations, Credit Blockers, & NBR Mushak 6.3 Challans. |
| **Phase 3** | 13 | Manufacturing | ✅ Completed | BOMs, Work Orders, Wastage Formulas, & NBR Mushak 4.3 Declarations. |
| **Phase 3** | 14 | Assets | ✅ Completed | Fixed Asset Registries & Monthly Depreciation Posting Engine. |
| **Phase 3** | 15 | Banking | ✅ Completed | SWIFT MT940 Parser & Automated Bank Reconciliation Adjustments. |
| **Phase 3** | 16 | Finance | ✅ Completed | Multi-Branch Consolidation & Fiscal Year-End Retained Earnings Closing. |
| **Phase 3** | 17 | Logistics | ✅ Completed | Global Intercompany Stock Transfers & Dual-Ledger Automated Clearing. |
| **Phase 3** | 18 | Compliance | ✅ Completed | Monthly NBR Mushak 9.1 Return Aggregator & TR-6 Treasury Deposits. |
| **Phase 3** | 19 | Adjustments | ✅ Completed | NBR VDS (Mushak 6.6) & Debit/Credit Note Processing (Mushak 6.7/6.8). |
| **Phase 3** | 20 | Multi-Currency | ✅ Completed | Exchange Rate Basis Registries & Month-End Unrealized Forex Revaluations. |

## 🔐 Tamper-Evident Cryptographic Ledger

RAAX protects financial history from administrative tampering or unauthorized direct database modifications using SHA-256 cryptographic hash-chaining.

### Chronological Hash-Chaining

Every journal posting is structurally linked to the state of the ledger preceding it:

$$H_n = \text{SHA-256}(H_{n-1} \mathbin{\Vert} \text{Payload\_Hash}_n)$$

### Integrity Auditing

Run the verification engine locally or in your CI/CD pipelines to validate general ledger integrity:

```bash
php artisan raax:ledger:verify {tenant_id}
```

## 🌍 Multi-Jurisdictional Tax Localization

RAAX features a dynamic, decoupled tax strategy architecture designed to support parallel tax ledgers and localization rules across international branches within a single database.

### Dynamic Tax Driver Resolution

Calculation logic is delegated to localized tax drivers via an extensible factory registry:

* **Bangladesh VAT Driver:** Generates NBR-compliant Mushak outputs.
* **India GST Driver:** Evaluates shipping states to automatically split transactions into CGST/SGST (intra-state) or IGST (inter-state) ledgers.
* **Europe VAT Driver:** Enforces destination-based European VAT rules.

## 📉 Budgetary Control & Commitment Accounting

RAAX protects organizational capital by running active, real-time budget verification checks directly during the procurement lifecycle.

### Funds Available Formula

Available funds are evaluated dynamically before any expenditure is authorized:

$$\text{Funds Available} = \text{Budget} - \text{Actual Expenditures} - \text{Encumbrances}$$

* **Encumbrances:** Funds earmarked at the approval stage of Purchase Orders to prevent budget overruns.
* **Relief:** Earmarked funds are atomically relieved and transferred to actual expenditures once goods receipts (GRNs) are completed.
