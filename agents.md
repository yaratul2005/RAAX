# AGENTS.md — RAAX ERP System Constitution

This file is the single source of truth for any AI agent (Google Jules, Copilot, or human developer) working on the RAAX codebase. Read this in full before generating, editing, or reviewing any code. If a task instruction conflicts with this file, this file wins — flag the conflict instead of silently resolving it.

---

## 1. What RAAX Is

RAAX is an open-source, on-premise/self-hosted **Enterprise Resource Planning (ERP)** system built for organizations of 1,200+ employees, designed to scale toward multi-entity/multi-branch holding structures over a 5-year horizon.

Core identity:
- API-first, security-centric, deterministic system of record.
- **Zero client-side AI in the runtime.** No chatbots, no LLM query bars, no generative summaries anywhere in the deployed application.
- Every transaction must be auditable, reversible in intent (via soft-delete + append-only logs), and enforce strict segregation of duties.

---

## 2. Tech Stack (Pinned — do not substitute without an ADR)

| Layer | Technology |
|---|---|
| Backend | PHP 8.3+, Laravel 11/12 |
| Frontend | Vue.js 3 + Inertia.js (SPA, no separate REST-consuming frontend app) |
| Primary DB | PostgreSQL 16 |
| Cache | Redis 7.2 |
| Queue | Redis Queues via Laravel Horizon |
| Search | OpenSearch 2.12 |
| Auth | OIDC / OAuth2 / JWT via Laravel Passport |
| Container/Infra | Docker → AWS EKS (Kubernetes), Terraform IaC |

Do not introduce a new framework, ORM, queue system, or frontend library without it being logged as an ADR in `/docs/adr/`.

---

## 3. Architecture Rule: Modular Monolith

RAAX is **one deployable codebase**, split into strict domain modules (not microservices):

```
Modules/
  Finance/
  HR/
  Procurement/
  Inventory/
  Sales/
  SystemCore/
```

Rules that must never be broken:
- A module may **not** directly query another module's database tables or call its internal classes.
- Cross-module communication happens **only** via:
  1. Defined service interfaces registered in Laravel's service container, or
  2. Asynchronous domain events (e.g. `PurchaseOrderAuthorized`, `SalesInvoiceFinalized`) consumed via queues.
- Each module owns its own Controllers, Service classes, Repositories, and Migrations.
- If a task seems to require reaching across module boundaries directly, stop and raise it — the correct fix is an event or interface, not a direct import.

---

## 4. Explicitly Out of Scope (do not build these, even if asked casually)

- Generative AI / natural-language UI / LLM query bars in the **application runtime**.
- Client-side predictive ML (forecasting, AI credit scoring, AI stock balancing).
- Manufacturing/MRP features outside Phase 3 (BOM, work orders, routing) — schema may be scaffolded early, but no functional logic before Phase 3.
- Any AI-assisted coding tool output going *into* the running app itself — AI tools (Jules, Copilot) are for the **dev cycle only**, never invoked from within RAAX at runtime.

---

## 5. Non-Negotiable Data & Security Rules

- **Row-Level Security (RLS)** enforces tenant isolation at the PostgreSQL engine level — never rely on application-side `WHERE tenant_id = ?` filters as the sole safeguard. Every multi-tenant table must have RLS enabled and forced (`ENABLE ROW LEVEL SECURITY` + `FORCE ROW LEVEL SECURITY`).
- DB connections use the `app_user` role with `NOBYPASSRLS NOSUPERUSER` — never a superuser role for application queries.
- Money is stored as **integer cents** + ISO 4217 currency code. Never use floats for currency.
- Deletes are **soft deletes** (`deleted_at`) — never hard-delete transactional or master data.
- All core tables follow **3NF**; pre-aggregated tables are only for reporting/read performance, not source of truth.
- Every mutation to a core table must be captured in the **append-only audit log** (user id, IP, timestamp, action, pre/post state).
- Secrets never get committed to the repo — always via AWS Secrets Manager / environment injection.
- Segregation of Duties (SoD): high-risk operations (payments, salary changes, inventory adjustments, procurement, permission changes) require a 3-step initiator → verifier → authorizer chain. Never collapse this into a single-role action.

---

## 6. API Conventions

- REST, JSON payloads, versioned routes: `/api/v1/{module}/{resource}`.
- Auth via OAuth2/JWT (Passport).
- Rate limiting: Redis-backed sliding window, default 120 req/window per client, returns `429` with standard `X-RateLimit-*` headers.
- Deprecated endpoints stay supported for at least 2 major release cycles.

---

## 7. Code Quality Bar

- Style: PSR-12, enforced via Pint (`vendor/bin/pint --test`).
- Static analysis: PHPStan level 8 (`vendor/bin/phpstan analyse --level=8`).
- Tests: `php artisan test --parallel`, coverage must stay ≥ 80% to merge.
- All of the above must pass in CI before merge to `main`. Trunk-based development — short-lived feature branches, feature flags for incomplete work, no long-lived branches.

---

## 8. Current Phase Context

RAAX ships in 4 phases over 20 months. **Do not build ahead of the current phase** unless explicitly scaffolding schema for a documented future phase.

- Phase 0 (Mo 1–2): Schema design, RLS policy verification, CI/CD setup.
- Phase 1 (Mo 3–8): Auth/OIDC/RBAC, Core Finance Ledger, HR records, AP/AR.
- Phase 2 (Mo 9–14): Procurement, Inventory, Sales, local tax engines.
- Phase 3 (Mo 15–20): MRP/BOM, branch stock transfers, asset registers.

Check `PHASE_0_SCOPE.md` (or the current phase's scope doc) for the active task list before starting new work.

---

## 9. When Unsure

If a task instruction conflicts with this file, or requires a decision not covered here (new dependency, cross-module shortcut, schema change affecting another module) — stop and surface the conflict rather than guessing. Log significant decisions as an ADR in `/docs/adr/`.
