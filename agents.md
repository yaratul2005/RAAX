# **File 1: README.md**

# **RAAX Enterprise Resource Planning Platform**

 ([https://img.shields.io/badge/Backend-Laravel\_12-red.svg](https://img.shields.io/badge/Backend-Laravel_12-red.svg))\](\#) ([https://img.shields.io/badge/Database-PostgreSQL\_16\_RLS-indigo.svg](https://img.shields.io/badge/Database-PostgreSQL_16_RLS-indigo.svg))\](\#) ([https://img.shields.io/badge/CI%2FCD-GitHub\_Actions-green.svg](https://img.shields.io/badge/CI%2FCD-GitHub_Actions-green.svg))\](\#) 

## **Executive Summary**

The RAAX Enterprise Resource Planning (ERP) platform serves as the definitive system of record and operational orchestration layer for the enterprise, built to scale cleanly to support multi-entity and multi-branch operations. Operating on an API-first, security-centric foundation, the system enables standardized, predictable processing for 1,200+ employees with zero client-side artificial intelligence interfaces.  
By implementing a Modular Monolith architecture using PHP 8.3 with the Laravel framework and PostgreSQL 16, RAAX balances database performance with design simplicity. This architecture groups code by business domain, keeping the system easy to maintain and test, while allowing modules to be split into standalone services in the future if operational demands change.

                                  \+-------------------+  
                                  |    Client SPA     |  
                                  \+---------+---------+  
                                            |  
                                  \+---------v---------+  
                                  |    API Gateway    |  
                                  \+---------+---------+  
                                            |  
               \+----------------------------+----------------------------+  
               |                                                         |  
\+--------------v--------------+                           \+--------------v--------------+  
|   Modules\\HR                |                           |   Modules\\Finance           |  
|   \- Employee Directory      |                           |   \- General Ledger          |  
|   \- Attendance Ledger       |                           |   \- AP/AR Aging             |  
\+--------------+--------------+                           \+--------------+--------------+  
               |                                                         |  
               \+----------------------------+----------------------------+  
                                            |  
                                  \+---------v---------+  
                                  | PostgreSQL Database|  
                                  | (Row-Level Sec.)  |  
                                  \+-------------------+

Data security is managed directly inside the database engine using PostgreSQL Row-Level Security (RLS) policies. RLS ensures that database queries can access rows matching only the active user context, preventing data leakage across branch operations at the query level.  
The deployment roadmap is structured into clear, manageable phases. Initial phases establish a secure foundation, integrating core finance and human resources modules. Subsequent releases expand capabilities to include procurement tracking, warehouse inventory management, and localized compliance tax reporting. Supported by a DevOps-minded engineering team, automated test suites, and robust cloud configurations on AWS, RAAX provides a stable digital core designed to scale alongside the enterprise for years to come.

## **Project Structure Overview**

The RAAX repository is organized using a domain-driven Modular Monolithic folder layout. Each business domain holds its own models, controllers, routes, migrations, and service providers:

raax-erp/  
├── app/  
│   ├── Console/  
│   ├── Http/  
│   │   └── Requests/  
│   │       └── BaseRequest.php  \# Unified validation response base  
│   └── Providers/  
│       └── AppServiceProvider.php  
├── bootstrap/  
│   └── providers.php  
├── config/  
├── database/  
├── modules/                      \# Domain encapsulated modules  
│   ├── Finance/  
│   │   ├── Controllers/  
│   │   ├── Database/  
│   │   │   └── Migrations/  
│   │   ├── Models/  
│   │   ├── Providers/  
│   │   └── Routes/  
│   ├── HR/  
│   └── Procurement/  
└── tests/

## **1\. Context and Goals**

### **1.1 Vision and Objectives**

The RAAX Enterprise Resource Planning platform establishes a single, cohesive transactional registry for a growing organization of 1,200+ employees. The core objective of RAAX is to consolidate fragmented operational datasets into an integrated, real-time data plane, eliminating reconciliation latencies, manual workflow handoffs, and data synchronization gaps. By utilizing a highly modular, decoupled architecture, the platform scales alongside the enterprise without incurring technical debt or system degradation.

\+------------------------------------------------------------------------+  
| Year 1: Ledger Integrity & Standardization                            |  
| \- Standardize charts of accounts, employee records, and inventory data. |  
| \- Deploy core HR and Double-Entry Finance modules.                     |  
| \- Achieve transactional auditability and eliminate paper processing.   |  
\+-----------------------------------+------------------------------------+  
                                    |  
                                    v  
\+------------------------------------------------------------------------+  
| Year 3: Operational Optimization & Automation                          |  
| \- Integrate Procurement, Sales, and localized tax engines.             |  
| \- Automate compliance document generation (e.g., Mushak forms).|  
| \- Reduce manual ledger audit preparation times by 70%.       |  
\+-----------------------------------+------------------------------------+  
                                    |  
                                    v  
\+------------------------------------------------------------------------+  
| Year 5: Holding Company & Transnational Scaling                        |  
| \- Scale RAAX to support multi-entity holding organizations.            |  
| \- Enable multi-currency and multi-jurisdictional tax consolidation.     |  
| \- Deploy multi-tenant, row-level isolated branch structures.|  
\+------------------------------------------------------------------------+

Over a five-year horizon, RAAX will evolve from a centralized single-entity platform to a multi-entity database. This progression is structured across three core milestones:

* **Year 1 (Ledger Integrity and Standardization):** Standardize all internal charts of accounts, employee master files, and physical stock directories. By the end of Year 1, all core HR profiles, attendance records, purchase orders, and double-entry financial journals must reside within RAAX. This eliminates legacy paper workflows and spreadsheet-based reporting, establishing absolute transactional audibility.  
* **Year 3 (Operational Optimization and Compliance Automation):** Achieve complete automation of the order-to-cash and procure-to-pay lifecycles. RAAX will integrate automated tax engines designed to handle localized, statutory calculations (such as sales tax, Value Added Tax (VAT), and withholding taxes) directly within transactional workflows. Automated generation of tax ledgers, tax invoices, and monthly returns reduces manual audit preparation times by 70%.  
* **Year 5 (Holding Company and Transnational Scaling):** Scale the RAAX architecture to support multi-entity holding company configurations, localized multi-currency accounting, and decentralized international branch operations. By utilizing database-level multi-tenancy rules and Row-Level Security (RLS) policies, RAAX will support distributed multi-country entities on a unified infrastructure footprint with zero risk of cross-tenant data leakage.

### **1.2 Scope and Boundaries**

The delivery lifecycle for RAAX is partitioned into logical development phases to guarantee focus, mitigate implementation risks, and ensure project tracking.

| Functional Area | Phase 1 (Core MVP) | Phase 2 (Extended ERP) | Phase 3 (MRP & Assets) | Future Roadmap |
| :---- | :---- | :---- | :---- | :---- |
| **System Core** | Single-database structure, OIDC authentication, RBAC, append-only logs | Dynamic feature flags, localized system settings, event-driven integrations | Dynamic form and field customization tools | Global multi-tenant database partitions |
| **Finance** | Dual-entry Chart of Accounts, General Ledger, Journals, AP/AR aging | Localized tax/VAT logic engines, automated invoices | MT940 bank reconciliation integrations, asset registers | Multi-entity cash flow consolidations |
| **HR & Payroll** | Employee master directory, roles, attendance, shift scheduling | Leave workflow approvals, basic payroll engine, benefit matrices | Appraisal workflows, KPI analytics, training management | International payroll localization |
| **Procurement** | Supplier register, Purchase Requests, Purchase Orders, basic GRN | Multi-tier PO approvals, item-to-supplier price matrices | Automated vendor scoring, dynamic reorder triggers | Automated multi-tenant supply networks |
| **Inventory** | Standard stock tracking, multi-warehouse support, FIFO valuation | Bin location matrices, barcode registration, stock corrections | Stock transfers with integrated transfer invoices | Global intercompany inventory routing |
| **Sales** | Customer master records, Sales Orders, basic invoicing | Dynamic sales pricing rules, credit terms, automated tax invoices | Service level invoice processing, credit notes | EDI-based sales pipeline integrations |
| **Manufacturing** | *Deferred / Out of Scope* | *Deferred / Out of Scope* | Bill of Materials (BOM), work orders, routing configurations | Shop floor data capture integrations |

To prevent scope creep and ensure focus on core ledger stability, the following features are **explicitly out of scope** for all initial development phases:

* **Generative AI & Natural Language UI Interfaces:** End-user conversational inputs, automated summarization tools, and LLM-driven query bars are completely excluded to maintain a deterministic, predictable, and fully auditable interface.  
* **Client-Side Predictive ML Models:** Predictive demand forecasting, AI-driven credit scoring, and automated inventory balancing are deferred. Run-time execution logic must remain strictly rules-based and auditable.  
* **Development-Only AI Exception:** AI-assisted IDE tools (such as GitHub Copilot or Google Jules) are permitted exclusively within the development cycle. They are utilized by engineers to write high-test-coverage code, generate test cases, and analyze static schemas, but they must not execute within the RAAX application runtime itself.

### **1.3 Stakeholders and Roles**

RAAX coordinates operations across multiple business departments, requiring a clear, shared access model. The permissions model enforces strict boundaries, ensuring users can access only the workflows and data necessary for their roles.

| User Persona Group | Key Business Department | Primary Access Mode | Permitted Platform Actions |
| :---- | :---- | :---- | :---- |
| **Executive Leadership** | C-Suite (CEO, CFO, COO) | Executive Dashboard | Read-only access to P\&L, consolidated cash flow, overall pipeline performance, and strategic dashboards. |
| **Department Heads** | HR, Finance, Procurement, Sales | Departmental Control Panel | Full CRUD permissions on department-specific configuration tables; multi-tier approvals of transactions. |
| **Operations Staff** | HR Associates, Sales Reps, Warehouse Clerks | Transaction Entry Form | Read/write access on standard data-entry screens; restricted from editing historic financial registers or master settings. |
| **System Administrators** | Corporate IT & Security Operations | Administration Console | Complete administrative control over identity providers, system configurations, feature flags, and log systems. |
| **Compliance Officers** | Internal & External Financial Auditors | Audit Portal | Read-only access to append-only database transaction logs, change histories, and statutory tax registers. |

## **2\. Functional Requirements (ERP Core)**

The core requirements of the RAAX ERP system are prioritized using the MoSCoW framework. Every module is defined by its data entities, workflow logic, validations, and standard reporting structures.

                      \+-----------------------------+  
                      |       Employee Master       |  
                      \+--------------+--------------+  
                                     |  
               \+---------------------+---------------------+  
               |                                           |  
\+--------------v--------------+             \+--------------v--------------+  
|     Attendance Ledger       |             |        Leave Registry       |  
\+-----------------------------+             \+-----------------------------+  
               |                                           |  
               \+---------------------+---------------------+  
                                     |  
                      \+--------------v--------------+  
                      |        Payroll Engine       |  
                      \+--------------+--------------+  
                                     |  
                      \+--------------v--------------+  
                      |        General Ledger       |  
                      \+-----------------------------+

### **2.1 Core Platform & Foundation**

* **Must Have:**  
  * **PostgreSQL Row-Level Security (RLS) Multi-Tenancy:** Automated separation of multi-entity databases using engine-level isolation rules to prevent data contamination.  
  * **Role-Based Access Control (RBAC):** Fine-grained permission mapping at both the API gateway and standard model controller layers.  
  * **Append-Only Transaction Logging:** A non-modifiable system registry recording all database mutations, tracking user identifiers, physical IP addresses, timestamps, and data changes.  
  * **Feature Flag Engine:** System configuration tools to enable or disable individual modules or features at runtime without requiring new code deployments.  
  * **Asynchronous Notifications:** A queue-driven messaging layer that dispatches in-app, SMTP email, and SMS alerts via background worker processes.  
* **Should Have:**  
  * **Single Sign-On (SSO):** Standard integration with enterprise Identity Providers via OpenID Connect (OIDC) and SAML 2.0.  
* **Could Have:**  
  * **Multi-Locale Engine:** Support for user-specific display languages, number formats, and date configurations.  
* **Won't-for-Now:**  
  * **Generative AI Assistance:** No natural-language UI commands or automated AI summary features will be supported.

### **2.2 Human Resources (HR)**

The Human Resources module manages employee lifecycles and establishes the organization's central directory.

| Component | Detail Specification |
| :---- | :---- |
| **Key Entities** | Employee, Department, Designation, AttendanceRecord, LeaveRequest, ShiftAssignment |
| **Core Workflows** | Employee onboarding, shift assignments, leave request validation, and attendance registration. |
| **Critical Validations** | Prevent overlapping shift schedules; block leave requests that exceed a user's accrued leave balance. |
| **Required Reports** | Daily Absentee Summary, Active Headcount Metrics, and Accrued Leave Balance Registers. |

                                  \+---------------------+  
                                  |     Core General    |  
                                  |    Ledger (ACID)    |  
                                  \+----------+----------+  
                                             |  
                      \+----------------------+----------------------+  
                      |                                             |  
           \+----------v----------+                       \+----------v----------+  
           |   Accounts Payable  |                       |  Accounts Receivable |  
           |      (AP) Aging     |                       |      (AR) Aging     |  
           \+---------------------+                       \+---------------------+  
                      |                                             |  
                      \+----------------------+----------------------+  
                                             |  
                                  \+----------v----------+  
                                  | Local Tax/VAT Engine|  
                                  | (Mushak Compliance) |  
                                  \+---------------------+

### **2.3 Finance & Accounting**

The Finance and Accounting engine enforces absolute transactional integrity across all accounts.

| Component | Detail Specification |
| :---- | :---- |
| **Key Entities** | ChartOfAccounts, GeneralLedgerAccount, JournalEntry, TaxRate, CostCenter, BankReconciliation |
| **Core Workflows** | Journal entries, Accounts Payable (AP) and Accounts Receivable (AR) management, and multi-rate VAT calculations. |
| **Critical Validations** | Ensure double-entry balances match exactly (![][image1]) on every journal write transaction; block adjustments to closed fiscal periods. |
| **Required Reports** | Real-time Balance Sheets, Profit & Loss Statements, Monthly VAT Returns (e.g., Mushak 9.1 data layouts), and AP/AR Aging Profiles. |

To support statutory localizations, such as Bangladesh VAT compliance, the Finance engine integrates specialized compliance reporting models:

* **Sales VAT Invoicing:** Generates automated tax invoices (such as Mushak 6.3) at the point of sale to ensure compliance with transport requirements.  
* **Purchase Register Automatons:** Auto-generates standard purchase registers (such as Mushak 6.1) to support input tax credit claims.  
* **VAT Deducted at Source (VDS):** Manages VDS certificate generation (such as Mushak 6.6) when withholding tax from suppliers.

### **2.4 Procurement & Inventory**

This module bridges physical inventory tracking with corporate accounting ledgers.

| Component | Detail Specification |
| :---- | :---- |
| **Key Entities** | VendorProfile, PurchaseRequest, PurchaseOrder, GoodsReceivedNote, StockItem, WarehouseBin |
| **Core Workflows** | Sourcing requests, purchase order approvals, receipt validation via GRN, and real-time inventory tracking. |
| **Critical Validations** | Reject GRN inputs if quantity received exceeds the remaining balance on the source PO by more than a 10% tolerance threshold. |
| **Required Reports** | Real-Time Stock Valuations (FIFO and Weighted Average), Reorder Alert Lists, Purchase Order Status Trackers, and Supplier Invoicing Reports. |

### **2.5 Sales & Distribution**

The Sales module manages customer order lifecycles and outbound transactional billing.

| Component | Detail Specification |
| :---- | :---- |
| **Key Entities** | CustomerProfile, Quotation, SalesOrder, DeliveryNote, SalesInvoice, CreditNote |
| **Core Workflows** | Customer quotations, order processing, delivery note creation, invoice generation, and sales return handling. |
| **Critical Validations** | Block sales order confirmations if the customer's total outstanding balance exceeds their approved credit limit. |
| **Required Reports** | Daily Billing Registers, Invoice Aging Reports, Customer Credit Logs, and Product Profitability Dashboards. |

### **2.6 Production / Operations**

* *Phase 3 In-Scope:* Defers full Manufacturing Resource Planning (MRP) to Phase 3, but builds database schemas to natively support these future domains.  
* **Must Have (Phase 3):** Bill of Materials (BOM) management, production work order scheduling, routing step configurations, and basic shop floor data tracking.  
* **Should Have (Phase 3):** Material Requirements Planning runtimes that calculate material shortfalls based on active production schedules.

### **2.7 Asset Management**

* *Phase 3 In-Scope:* Defers corporate asset tracking to Phase 3 to prioritize core ledger operations.  
* **Must Have (Phase 3):** Fixed Asset Registers tracking item descriptions, cost centers, acquisition details, and disposal events.  
* **Should Have (Phase 3):** Automated depreciation engines executing straight-line and double-declining depreciation calculations on set schedules.

### **2.8 Reporting & Analytics**

* **Must Have:**  
  * **Standard Module Reports:** Standard pre-built operational summaries for each active functional module.  
  * **Unified Export Engine:** Direct data export of tables and reports to Excel, CSV, and PDF formats.  
  * **Executive Dashboards:** High-density visual metrics demonstrating key financial, sales, and inventory metrics.  
* **Should Have:**  
  * **Ad-Hoc Query Engine:** Simple UI builders allowing users to group, filter, and aggregate standard tables without writing custom SQL.

### **2.9 System Administration**

* **Must Have:**  
  * **Unified Identity Directory:** Central tools to manage user accounts, assign roles, and configure system access parameters.  
  * **Real-time Audit Console:** An interface for administrative and audit teams to query, filter, and export the system's append-only change logs.  
  * **Platform Telemetry Overview:** Dashboards mapping Active Web Sockets, queue depths, job failure rates, and database connections.  
  * **Automated Backup Controls:** Conceptual management controls to verify daily database backup success and schedule off-peak recovery tests.

## **3\. Non-Functional Requirements**

### **3.1 Performance**

* **Concurrent Users:** System architecture must support 1,200 total employees, with standard active user concurrency targets modeled at 200 to 400 active concurrent connections.  
* **Response Time Targets:**  
  * Read queries for typical UI screens (excluding heavy reporting dashboards) must achieve:![][image2]  
  * Write and transactional update transactions must complete within:![][image3]  
  * Heavy analytics dashboards or consolidated reports must render in under:![][image4]  
* **Batch Job Windows:** Long-running system processes (such as payroll calculation steps, stock valuation sweeps, and ledger closing routines) must execute during off-peak hours (01:00 to 03:00) and complete within:![][image5]

### **3.2 Scalability**

* **Horizontal Application Scaling:** Web application pods must run stateless inside containers, scaling dynamically based on average CPU and memory metrics:  
  ![][image6]  
* **Database Scaling:** Primary-replica database configurations are used. All write transactions route to the primary write instance, while read queries are load-balanced across multiple read-replicas.  
* **Data Growth Resilience:** Database schemas are structured to manage data expansion of 1.5 GB per month. Large-volume log and audit tables utilize PostgreSQL partitioning based on timestamp ranges, keeping active queries fast as database size grows.

### **3.3 Reliability & Availability**

* **System Availability:** Target system availability is set at 99.9% uptime over any calendar year, allowing a maximum unplanned outage limit of:  
  ![][image7]  
* **Recovery Targets:**  
  * **Recovery Time Objective (RTO):** ![][image8] to restore complete operational capabilities after an infrastructure outage.  
  * **Recovery Point Objective (RPO):** ![][image9] of potential data loss liability, secured via continuous WAL stream replication to geographically isolated targets.  
* **Failover Design:** Automated health checks at the load balancer level automatically route traffic to the disaster recovery site if the primary infrastructure zone becomes unresponsive.

### **3.4 Security**

* **Authentication & MFA:** OpenID Connect (OIDC) authentication is required for all users, integrated with enterprise identity directories. Accounts with finance, payroll, or administrative permissions must use hardware token Multi-Factor Authentication (MFA).  
* **Data Encryption:** All data in transit must use TLS 1.3 encryption. Data at rest must be encrypted using AES-256-GCM at the storage and database layer.  
* **Secrets Isolation:** Application secrets, database credentials, and external API keys must be stored in secure systems such as AWS Secrets Manager or HashiCorp Vault. Sensitive keys are never committed to the code repository.  
* **Compliance & Privacy:** To support data protection guidelines, RAAX provides automated tools to mask, archive, or permanently purge PII datasets based on configurable retention schedules.  
* **Patching and Vulnerabilities:** Automated vulnerability scanning runs weekly on all container images and code libraries. Security patches for core dependencies are deployed during scheduled weekly maintenance windows.

### **3.5 Maintainability & Extensibility**

* **Domain Isolation Rules:** Core application domains are decoupled. Modules must interact only through defined service interfaces or asynchronous domain events to prevent circular dependencies.  
* **API Stability:** API endpoints use semantic versioning (such as /api/v1/). Deprecated endpoints must be supported for at least two major release cycles before removal.  
* **Code Quality Standards:** Developers must follow standard PSR-12 style rules. Code coverage by automated tests is checked during integration and must remain above 85% to merge new updates.

### **3.6 Usability & UX Principles**

* **Design Language:** Uses a professional, high-contrast, high-density corporate layout. Minimal padding and responsive data grids maximize visible information on screen.  
* **Consistency:** Form inputs, action buttons, validation error messages, and table interfaces follow a shared UI pattern library across all modules.  
* **Accessibility:** Interfaces are designed to comply with WCAG 2.1 Level AA accessibility standards, featuring clear contrast ratios and keyboard-first navigation patterns for high-speed data entry.

### **3.7 Observability**

* **Structured Logs:** Applications must output structured JSON logs containing standardized metadata:  
  JSON  
  {  
    "timestamp": "2026-03-31T15:04:05Z",  
    "correlation\_id": "tx-88a2-9901-bf3c",  
    "environment": "production",  
    "module": "Finance",  
    "action": "InvoiceCommitted",  
    "user\_id": "usr-4412-bd22",  
    "duration\_ms": 114  
  }

* **Distributed Tracing:** Every HTTP request is assigned a unique correlation ID at the API gateway. This ID is passed to all internal processes, queues, and database connections to simplify debugging.  
* **Application Metrics:** System dashboards track core performance metrics, including request latencies (![][image10], ![][image11], ![][image12]), database connection counts, Redis memory usage, queue latency, and transaction error rates.

## **4\. Architecture & Technology Strategy**

### **4.1 High-Level Architecture**

To support a growing organization of 1,200+ employees, RAAX is designed as a **Modular Monolith**. This architecture provides clean logical separation while avoiding the operational complexity of distributed microservices.

#### **Decision: Modular Monolith vs. Microservices**

* **Decision:** Implement a single deployable code repository organized into independent modules (e.g., Modules\\Finance, Modules\\HR, Modules\\Procurement) with strict domain boundaries.  
* **Rationale:** Microservices add significant overhead, including network latency, complex distributed data management, and orchestration challenges. A modular monolith groups code by domain, running within a single process and database. This approach maintains a simple deployment pipeline while allowing individual modules to be split into separate services in the future if scaling needs change.

### **4.2 Technology Stack**

The core technologies for RAAX are selected to balance operational reliability, modern capabilities, and developer productivity:

| Technology Layer | Selected Component | Architectural Rationale |
| :---- | :---- | :---- |
| **Backend Framework** | PHP 8.3+ with Laravel 11/12 | Provides robust dependency injection, simple routing, database migration tools, and an active ecosystem for enterprise applications. |
| **Frontend Framework** | Vue.js 3 with Inertia.js | Delivers a responsive Single Page Application (SPA) experience. Inertia.js links server-side controllers directly with frontend views, reducing API state management complexity. |
| **Primary Database** | PostgreSQL 16 | Relational database engine supporting robust ACID transactions, native JSONB structures, and advanced Row-Level Security (RLS) policies. |
| **In-Memory Cache** | Redis 7.2 | High-throughput cache used for session storage, query caching, dynamic rate-limiting counters, and background task management. |
| **Message Broker** | Redis Queues (Horizon) | Manages background jobs and asynchronous tasks. Provides visual monitoring of queue health and failed tasks. |
| **Search Index Engine** | OpenSearch 2.12 | Offloads complex search and reporting queries from the primary database, providing fast text indexing and operational search capabilities. |

### **4.3 API Strategy**

RAAX is built on an API-first design. All user interfaces and external integrations use the same underlying REST API endpoints, ensuring consistency and simplifying system audits.

* **Endpoint Design:** API resources follow consistent REST patterns and require JSON payloads.  
* **Version Management:** URI routing enforces semantic versioning: /api/v1/procurement/purchase-orders.  
* **Authorization:** API connections are secured using OAuth 2.0 / JWT tokens generated via Laravel Passport.  
* **Rate Limiting Mechanics:** A Redis-backed sliding-window counter rate-limits requests at the API gateway to prevent resource exhaustion. The default user limit is set to 120 requests/minute. The mathematical evaluation for each client connection ![][image13] at time ![][image14] is defined as:  
  ![][image15]  
  Where ![][image16] and ![][image17] represents incoming requests. Connections that exceed this threshold receive an HTTP 429 Too Many Requests response with standard rate-limit headers:  
  HTTP  
  X-RateLimit-Limit: 120  
  X-RateLimit-Remaining: 0  
  X-RateLimit-Reset: 1711902781

### **4.4 Integration Points**

To maintain system stability, external integrations use decoupled, asynchronous patterns rather than blocking direct calls:

* **External Payment Gateways:** Integrations utilize webhook notifications and queue-based background processes to reconcile payments without blocking user workflows.  
* **Tax Authorities & E-Invoicing:** Transactions automatically queue compliance data payloads. These are processed asynchronously and sent to official NBR servers to avoid performance issues during high-volume sales.  
* **Banking Gateways:** Reconciles transactions using secure, scheduled background jobs that fetch and process bank files (e.g., MT940 or OFX) during off-peak hours.  
* **Communications Engines:** SMTP email deliveries and SMS alerts are queued and managed via Redis background workers.

### **4.5 Data Model Principles**

* **Normalization Strategy:** Master data and transactional tables follow Third Normal Form (3NF) design to prevent data duplication and protect relational consistency. Performance-critical reporting data is stored in pre-aggregated tracking tables.  
* **Soft Deletes:** Deleting records uses soft deletes (deleted\_at timestamps) instead of direct row deletion, preserving historical audit trails across all modules.  
* **Multi-Currency Support:** Financial fields store values as integer cents alongside three-letter ISO 4217 currency codes to prevent floating-point calculation errors and simplify multi-currency reporting.

## **5\. DevOps, Environments & CI/CD**

### **5.1 Repositories & Branching**

The RAAX system is developed and managed within a single monorepo hosted on GitHub, ensuring consistent deployments and simple dependency management.

* **Branching Workflow:** The project uses a trunk-based development workflow. Developers work on short-lived branches created from main. Features are managed using system feature flags, allowing code to be integrated regularly.  
* **Pull Request Verification:** Code merges to the main branch require passing validation checks, including style guides, static code analysis, unit test suites, and sign-off from an enterprise architect.

### **5.2 Environments Configuration**

RAAX utilizes four isolated environments to support development, testing, and production workflows.

| Environment | Host Infrastructure | Database Target | Data Security Rules | Access Scope |
| :---- | :---- | :---- | :---- | :---- |
| **Local** | Developer Workstation Docker Instances | Local Docker database instance | Synthetic development seed records only | Private Developer |
| **Staging** | Isolated AWS ECS Cluster | Isolated Staging Database Instance | Anonymized data snapshots from production | QA and Engineering Teams |
| **UAT** | Isolated AWS ECS Cluster | Isolated UAT Database Instance | Anonymized data snapshots from production | Department Leads and Stakeholders |
| **Production** | Auto-scaling AWS EKS Cluster | Primary Multi-AZ PostgreSQL 16 cluster | Active live business transaction database | Production Services and End-Users |

To maintain data privacy and comply with security policies, non-production environments are forbidden from containing active personal details, customer identifiers, or financial records. Non-production databases are populated using anonymized and scrubbed production database snapshots.

### **5.3 CI/CD Pipelines**

All deployment workflows are automated using GitHub Actions.

\+-------------------------------------------------------------+  
|                       GitHub Commit / PR                    |  
\+------------------------------+------------------------------+  
                               | Triggers CI Pipeline  
\+------------------------------v------------------------------+  
|                    Validation & Test Runner                 |  
|  \- Run PHPStan Static Analysis                |  
|  \- Execute Automated Test Suite (Unit/Integration)|  
|  \- Verify Code Standard (Pint)         |  
\+------------------------------+------------------------------+  
                               | Merged to main  
\+------------------------------v------------------------------+  
|                     Container Compilation                   |  
|  \- Compile and tag production Docker image                  |  
|  \- Push Docker image to AWS ECR registry                    |  
\+------------------------------+------------------------------+  
                               | Initiates Rolling Deploy  
\+------------------------------v------------------------------+  
|                     Production Deployment                   |  
|  \- Apply database schema updates in isolation  |  
|  \- Update Kubernetes Deployment Pod Images                   |  
\+-------------------------------------------------------------+

* **Automated Testing Requirements:** The CI pipeline runs dynamic validation suites on every pull request, executing static code analysis, style formatting, and the full test suite:  
  Bash  
  composer install \--no-interaction \--prefer-dist  
  vendor/bin/pint \--test  
  vendor/bin/phpstan analyse \--level=8  
  php artisan test \--parallel

* **Docker Containerization:** Successful builds are packaged as Docker container images and pushed to the AWS Elastic Container Registry (ECR) tagged with the unique Git commit SHA.  
* **Database Migrations:** DB migrations are verified in isolation before updating application pods:  
  Bash  
  kubectl exec \-it deployment/raax-migration-job \-- php artisan migrate \--force

* **Zero-Downtime Deployments:** Deployments run in rolling updates on the AWS EKS Kubernetes cluster, updating container pods with the target image tag without interrupting active users.

### **5.4 Infrastructure & Hosting**

RAAX runs on AWS, with all cloud assets configured as infrastructure-as-code (IaC) using Terraform.

* **Compute Plane:** Stateless PHP container pods run on Amazon EKS (Kubernetes) across multiple availability zones.  
* **Database:** PostgreSQL runs on Amazon RDS in a Multi-AZ cluster, with read-replicas for load balancing.  
* **Secrets Storage:** Dynamic secrets and credentials are encrypted and injected into containers at runtime using AWS Secrets Manager, keeping configuration values secure.

### **5.5 Monitoring & Incident Management**

* **Platform Dashboard:** Prometheus and Grafana track server resource usage, request latency, and application health.  
* **Logging:** Logs are aggregated and indexed to support search and diagnostics.  
* **Alerting Rules:** Critical exceptions or errors trigger notifications to Slack channels, while critical infrastructure failures route to on-call jobs via automated paging systems.

## **6\. Security, Controls & Compliance**

### **6.1 Access Controls**

Security in RAAX is enforced through a strict defense-in-depth model, starting at the API gateway and extending down to database row-level permissions.

\+-------------------------------------------------------------+  
|                        Incoming Web Request                 |  
\+------------------------------+------------------------------+  
                               |  
\+------------------------------v------------------------------+  
|                      OIDC/OAuth Gateway                     |  
|  \- Verify identity signature and access token JWT           |  
\+------------------------------+------------------------------+  
                               | Pass Validation  
\+------------------------------v------------------------------+  
|                    Application RBAC Middleware              |  
|  \- Check permissions for the user role against current path |  
\+------------------------------+------------------------------+  
                               | Pass Validation  
\+------------------------------v------------------------------+  
|                    PostgreSQL Connection Pool               |  
|  \- Execute: set\_config('app.tenant\_id', 'usr\_tenant')|  
\+------------------------------+------------------------------+  
                               | Enforces Isolation Policy  
\+------------------------------v------------------------------+  
|                   Row-Level Security Filter                 |  
|  \- Restricts data visibility to user's assigned branch|  
\+-------------------------------------------------------------+

#### **Decision: PostgreSQL Row-Level Security for Enterprise Security**

* **Decision:** Implement multi-tenant data separation directly in the database engine using PostgreSQL Row-Level Security (RLS) instead of manual database queries.  
* **Rationale:** Manual application queries (such as adding WHERE tenant\_id \= x filters) are vulnerable to coding errors, which can result in data leakage. Enabling RLS on PostgreSQL tables ensures that the database engine enforces isolation for every query, regardless of the application-level implementation.

Database connections use a dedicated non-superuser role (app\_user) configured with the NOBYPASSRLS attribute. When a request is initialized, RAAX resolves the tenant context and sets the session value on the connection:

SQL  
\-- Enforce security validation using isolated credentials  
CREATE ROLE app\_user WITH LOGIN PASSWORD 'strong\_password' NOBYPASSRLS NOSUPERUSER;

\-- Session initialization function  
CREATE OR REPLACE FUNCTION set\_tenant\_context(tenant\_uuid UUID) RETURNS VOID AS $$  
BEGIN  
    PERFORM set\_config('app.current\_tenant\_id', tenant\_uuid::TEXT, FALSE);  
END;  
$$ LANGUAGE plpgsql;

\-- Apply the RLS policy to the target table  
ALTER TABLE accounting\_journals ENABLE ROW LEVEL SECURITY;  
ALTER TABLE accounting\_journals FORCE ROW LEVEL SECURITY;

CREATE POLICY journal\_tenant\_isolation ON accounting\_journals  
    FOR ALL  
    TO app\_user  
    USING (tenant\_id \= NULLIF(current\_setting('app.current\_tenant\_id', TRUE), '')::UUID);

#### **Segregation of Duties (SoD) Verification Matrix**

To prevent unauthorized transactions, critical processes require verification and approval across separated user roles.

| Operational Process | Step 1: Initiator Role | Step 2: Verification Role | Step 3: Authorization Role |
| :---- | :---- | :---- | :---- |
| **Supplier Payments** | Accounts Operator | Finance Manager | Finance Director / CFO |
| **Salary Appraisals** | HR Associate | HR Director | Finance Director / CEO |
| **Inventory Adjustments** | Warehouse Clerk | Warehouse Manager | Operations Director |
| **Supplier Procurement** | Purchasing Agent | Department Head | Procurement Director |
| **System Permission Mods** | IT Administrator | Information Security Manager | Compliance Officer |

### **6.2 Audit & Traceability**

* **Append-Only System Log:** Core system tables utilize event triggers to write all changes to an append-only audit log, ensuring complete transactional traceability.  
* **Audit Metadata Requirements:** System audits capture the change timestamp, target record ID, active user ID, client IP address, action type, and complete pre- and post-transaction states.  
* **Log Integrity Strategy:** Audit logs are mirrored to write-once, read-many (WORM) storage volumes in Amazon S3, preventing modifications or deletions.

### **6.3 Data Protection**

* **Backup Retention Policies:** Primary databases run daily snapshots with a 30-day retention window. Write-ahead logs (WAL) are backed up continuously to support precise Point-In-Time-Recovery (PITR).  
* **Recovery Verification Drill:** Recovery procedures are tested automatically on an isolated staging cluster once per week, validating recovery times and reporting the results to administrative dashboards.  
* **Field-Level Encryption:** Sensitive personal details (such as taxpayer identifiers, banking credentials, and salary amounts) are encrypted at rest using AES-256-GCM.

### **6.4 Change Management**

* **Production Releases:** Deployment windows are scheduled during off-peak hours (Saturdays 02:00 to 04:00) to minimize business impact.  
* **Approval Gateways:** Releases require automated test passes and authorization from both the DevOps Lead and Enterprise Architect.  
* **Rollback Strategy:** Pipelines support immediate automated rollbacks. If deployment health checks fail post-release, the system automatically reverts container images to the previous stable release.

## **7\. Customization & Extensibility Model**

### **7.1 Configuration vs Customization**

To simplify maintenance and ensure smooth system upgrades, RAAX separates user configurations from core application code.

* **Dynamic UI Schema Engine:** Form field arrangements, layout settings, and dashboard displays are stored as metadata configurations within database-managed JSON schemas, rather than as custom application code.  
* **Dynamic Workflow Engine:** Business approval paths (such as procurement levels or purchase workflows) are managed via configuration rules, allowing departments to alter workflows without redeploying code.

### **7.2 Extension Mechanisms**

Custom business logic integrates with the RAAX platform through decoupled extension points, preventing custom code from introducing security or performance risks to the core engine.

                  \+-----------------------------------+  
                  |           Core Codebase           |  
                  |     (Locked System Modules)       |  
                  \+-----------------+-----------------+  
                                    |  
            \+-----------------------+-----------------------+  
            | Reads Configuration                           | Dispatches Events  
            v                                               v  
\+-----------------------+                       \+-----------------------+  
|  JSON Field Schemas   |                       |    Tenant Module Hooks|  
|  & Custom Workflows   |                       |  (Isolated Extensions)|  
\+-----------------------+                       \+-----------------------+

* **Asynchronous Event Subsystem:** System processes dispatch clear domain events (such as PurchaseOrderAuthorized or SalesInvoiceFinalized). Custom plugins or integrations subscribe to these events through background queues, ensuring that failure in custom code does not impact core execution.  
* **Static Interface Contracts:** Custom adapters must implement strict PHP interface definitions, registered with Laravel's service container:  
  PHP  
  namespace App\\Contracts\\Procurement;

  interface SupplierValidationInterface {  
      public function validateSupplierTIN(string $tinValue): bool;  
  }

### **7.3 Upgrade Strategy**

The upgrade pipeline ensures that core system packages can be updated continuously while maintaining backward compatibility.

* **Database Schema Evolution:** Database changes are designed around the "expand-and-contract" pattern to prevent application downtime during deployments:  
  * *Expand Phase:* Add new columns as nullable and deploy updated code that dual-writes to both the old and new fields.  
  * *Transition Phase:* Run background migration processes to backfill existing records.  
  * *Contract Phase:* Remove the obsolete columns from the database once all services are updated.  
* **Version Control Policies:** Custom integrations use semantic versioning policies, preventing breaking changes across core application updates.

## **8\. UI/UX & Branding Guidelines**

### **8.1 Design Principles**

The RAAX user interface is tailored for professional power users, focusing on readability, system consistency, and speed.

* **Information Density Strategy:** Data tables use high-density layouts with compact rows and minimal padding to maximize visible information.  
* **Clear Visual Hierarchy:** High-contrast design principles guide users through dense transactional datasets, making screens easy to read.  
* **Keyboard Navigation:** Input forms and grids support complete keyboard navigation, allowing data-entry operators to navigate, save, and submit records without relying on point-and-click mouse interactions.

### **8.2 Visual Language**

The interface uses a conservative, high-contrast color palette designed for legibility and prolonged professional use.

* **Corporate System Color Palette:**

| Color Class | HEX Value | Visual Interface Application | Contrast Ratio |
| :---- | :---- | :---- | :---- |
| **Primary Navy** | \#1E293B | Global navigation sidebars, headers, structural layout wrappers | 7.1:1 against Slate Light |
| **Accent Teal** | \#0F766E | Active selections, action elements, primary CTA states | 4.8:1 against white bg |
| **Text Charcoal** | \#0F172A | Primary text, labels, data cell metrics | 12.3:1 against white bg |
| **Slate Light** | \#F8FAFC | Grid canvas backgrounds, default form fields, card frames | Background Base |
| **Alert Red** | \#991B1B | Critical system error indicators, delete confirmations | 5.2:1 against light bg |

* **Typography System:** Standardized using clean sans-serif system fonts (such as Inter or Roboto) with explicit scale sizes:  
  * *Workspace Headings (H1):* 24px (Bold, Charcoal)  
  * *Section Titles (H2):* 18px (Semi-Bold, Navy)  
  * *Data Cell Typography:* 13px (Medium, Charcoal)  
  * *System Labels & Tooltips:* 11px (Regular, Slate Dark)  
* **Iconography Style:** Simple SVG vector icons with a uniform stroke weight (such as Lucide or Heroicons). Playful or colorful visual elements are excluded.

### **8.3 Layout & Navigation**

* **Primary Sidebar Interface:** Navigation is managed via a left sidebar, grouping main modules (Finance, HR, Procurement) and collapsed menu panels for submenu items.  
* **Workspace Tab System:** The primary dashboard features a multi-tab system, allowing users to keep multiple views (such as accounts charts and invoice forms) open concurrently without losing active inputs.  
* **Dynamic Command Palette:** Accessing system commands is simplified through a unified search panel (Ctrl \+ K), providing shortcuts to jump directly to modules, records, or help articles.

\+--------------------------------------------------------------+  
|  Global Search Dashboard (Ctrl \+ K)     \[Profile\]   |  
\+--------------------------------------------------------------+  
| |  |  
|           \+--------------------------------------------------+  
| Finance   |  Journal ID | Description   | Debit    | Credit  |  
|           |-------------+---------------+----------+---------|  
| HR        |  JRNL-8821  | Sales Posting | $4,500   | $0.00   |  
|           |  JRNL-8822  | Bank Deposit  | $0.00    | $4,500  |  
| Inventory |             |               |          |         |  
\+--------------------------------------------------------------+

### **8.4 Customizable Views**

* **Grid Column Management:** Users can customize active columns, toggle grid views, and adjust table widths.  
* **Saved Filters Matrix:** Users can save custom filter queries and search criteria, storing their configurations directly in the database to load dynamically.

## **9\. Project Plan & Delivery Roadmap**

### **9.1 Phases & Milestones**

The RAAX development roadmap is structured across four key phases, establishing a stable foundation before integrating subsequent operational modules.

Phase 0: Discovery & Architecture (Month 1-2)  
  ├── Schema definition and RLS policy verification  
  └── Setup CI/CD environments and repository models  
Phase 1: Foundation & Core Modules (Month 3-8)  
  ├── Deploy authentication, OIDC SSO, and RBAC configurations  
  └── Release Core Finance Ledger, HR records, and AP/AR tools  
Phase 2: Supply Chain & Sales Integration (Month 9-14)  
  ├── Deploy Procurement, Inventory Management, and Sales modules  
  └── Integrate local tax engines and automatic invoicing  
Phase 3: MRP & Multi-Branch Scaling (Month 15-20)  
  ├── Launch BOM tracking, branch stock transfers, and asset registers  
  └── Transition remaining legacy platforms to RAAX

* **Phase 0: Discovery and Architecture (Months 1–2):** Define backend structures, map data schemas, and test database RLS policies. Deliver base environment configurations and CI/CD pipelines.  
* **Phase 1: Foundation & Core Modules (Months 3–8):** Deploy identity registries (SSO/RBAC), basic HR directories, and the core double-entry finance ledger (journals, ledger accounts, and AP/AR aging matrices).  
* **Phase 2: Supply Chain & Sales Integration (Months 9–14):** Integrate procurement workflows, multi-warehouse inventory, customer orders, and localized compliance tax calculation engines.  
* **Phase 3: MRP & Multi-Branch Scaling (Months 15–20):** Deploy advanced production tracking (BOM), branch stock transfers, and asset registers. Finalize legacy system migrations and decommission legacy tools.

### **9.2 Team Structure**

Building RAAX requires a cross-functional development and operations team of 11 dedicated professionals:

* **Principal Enterprise Architect & PM (1):** Oversees database schemas, system architecture designs, and release milestones.  
* **DevOps / Infrastructure Specialist (1):** Configures AWS EKS infrastructure, manages Terraform deployment assets, and monitors CI/CD pipelines.  
* **Backend Engineers (4):** Implement core business logic, write APIs, and build database models and test suites.  
* **Frontend Specialists (3):** Build responsive data views, design layout components, and optimize UI performance and accessibility.  
* **Quality Assurance Engineers (2):** Write automated integration tests and perform end-to-end security validations.

### **9.3 Project Timeline and Dependencies**

* *Timeline Overview:* Development is scheduled over a 20-month timeline, leading to a complete production launch at the end of Month 20\.  
* *Critical Path Dependencies:* Setting up the core identity layer and database-level RLS policies is a critical path dependency that must be finalized in Phase 0 before beginning Phase 1 functional modules. Similarly, completing the Finance Ledger domain in Phase 1 is a hard dependency for integrating the purchasing and inventory engines in Phase 2\.

### **9.4 Risk Management**

The project tracks three primary technical and operational risks:

* **Data Migration Quality Risk:** Importing incomplete legacy data can lead to errors in the primary financial ledger.  
  * *Mitigation:* Create automated ETL pipelines that run continuous data integrity checks, validating ledger balances against source records before final database imports.  
* **Scope Creep Risk:** Adding unauthorized features during development can delay planned milestones.  
  * *Mitigation:* Establish strict governance policies that block changes to the phase baseline without review and sign-off from the project steering committee.  
* **Performance Bottleneck Risk:** Heavy database queries on large tables can increase system latency.  
  * *Mitigation:* Enforce query performance budgets within CI/CD pipelines, blocking code updates that trigger full table scans or exceed set query execution limits.

## **10\. Documentation & Knowledge Management**

### **10.1 Technical Documentation**

* **Architectural Decision Records (ADRs):** Major design choices, framework selections, and database configurations are documented in markdown files inside the /docs/adr repository directory.  
* **Interactive API Documentation:** API specifications are managed via OpenAPI 3.0 standards, auto-generating interactive Swagger UI dashboards directly from code components.  
* **Mermaid Schema Diagrams:** System entity relationships and data workflows are defined inside markdown files using Mermaid syntax, ensuring diagrams are updated during code merge cycles.

### **10.2 User Documentation**

* **Onboarding Manuals:** User training guides are tailored to specific roles and made accessible within the application workspace.  
* **Contextual Help System:** Form inputs and data grids include clear, inline tooltips explaining validation rules, reducing data entry errors.

### **10.3 Operations Documentation**

* **Operational Runbooks:** Step-by-step incident response runbooks document database failover steps, backup restoration steps, and horizontal container scaling procedures.  
* **Automated System Playbooks:** Infrastructure configurations, build steps, and environment settings are maintained as declarative playbooks inside deployment repositories, supporting reproducible system builds.

## **11\. Success Metrics & Governance**

### **11.1 Key Performance Indicators**

The RAAX platform monitors operational and technical KPIs to track system health and business value.

| Performance Focus | Targeted System Metric | Evaluation Interval | Measurement Tooling |
| :---- | :---- | :---- | :---- |
| **System Uptime** | Maintain ![][image18] core system availability | Monthly review | AWS Route 53 health reports |
| **Database Latency** | **![][image2]** query latencies | Continuous tracking | Prometheus monitoring dashboards |
| **Queue Completion** | 99.99% background task execution rates | Daily monitoring | Laravel Horizon administration console |
| **Data Processing** | Zero manual ledger reconciliation errors | Monthly review | Accounting reconciliation tests |
| **Audit Readiness** | 70% reduction in annual audit compilation times | Yearly review | Audit cycle duration trackers |

### **11.2 Governance Model**

* **ERP Steering Committee:** Features representatives from executive leadership, finance, HR, and operations. The committee meets monthly to prioritize features, review project milestones, and coordinate cross-department integrations.  
* **Change Control Board:** Includes product leads, security experts, and DevOps managers. The board reviews and authorizes production releases, manages environment configurations, and coordinates disaster recovery testing.  
* **Operational Review Cadence:** Technical teams hold weekly stand-ups to track progress, while executive steering groups run monthly reviews to evaluate KPIs, budget alignment, and long-term release schedules.

# **File 2: agents.md**

# **Google Jules Developer Agent: Core Architecture & Implementation Directives**

This document defines the immutable architectural boundaries, database security schemas, coding patterns, and compliance requirements that the Google Jules developer agent **must** follow when analyzing, writing, refactoring, or generating code within the RAAX ERP repository.

## **1\. Immutable Principles (Enforced by Jules Memories)**

The following directives are locked into active memory and override any standard generation defaults:

* **Database-Level Tenant Isolation:** Always enable and force PostgreSQL Row-Level Security (RLS) on any new multi-tenant or branch-scoped table. Never rely on a raw WHERE tenant\_id application filter alone.  
* **Strict Module Boundary Decoupling:** Never let one module (Finance, HR, Procurement, Inventory, Sales) directly query or import classes/models from another module.  
* **Decoupled Communication Pattern:** All cross-module communication must take place via registered service contracts/interfaces or through asynchronous domain events.  
* **Financial Precision:** Store all currency and monetary fields as integer cents (or local equivalent) accompanied by a three-letter ISO 4217 currency code column. Never use float or decimal types for money fields to prevent rounding errors.  
* **Continuous Audit Trails:** Never hard delete records. Always use Eloquent's SoftDeletes trait to maintain database schema auditability.

## **2\. Directory Layout & Namespace Standards**

All code generated for modular business domains must reside within the root /modules directory, using strict domain-driven namespaces:

modules/  
├── Finance/  
│   ├── Controllers/  
│   ├── Database/  
│   │   └── Migrations/  
│   ├── Models/  
│   ├── Providers/  
│   └── Routes/  
├── HR/  
│   └──... (same modular structure)  
└── Procurement/  
    └──... (same modular structure)

* **Autoloading:** PSR-4 must be mapped so that Modules\\Finance\\ references /modules/Finance.  
* **Registration:** Modules must register their own routes, config files, and database migrations dynamically through their respective domain ServiceProvider.

## **3\. Database Security Setup (PostgreSQL RLS)**

When creating database migrations for multi-tenant or multi-branch features, Jules must execute raw SQL blocks to enforce PostgreSQL Row-Level Security (RLS):

1. **Enable and Force RLS:**  
   SQL  
   ALTER TABLE table\_name ENABLE ROW LEVEL SECURITY;  
   ALTER TABLE table\_name FORCE ROW LEVEL SECURITY;

2. **Define Row Security Policies:** Enforce isolation using a dedicated application connection role (app\_user configured with NOBYPASSRLS and NOSUPERUSER) and session-level tenant variables:  
   SQL  
   CREATE POLICY table\_name\_tenant\_isolation ON table\_name  
       FOR ALL  
       TO app\_user  
       USING (tenant\_id \= NULLIF(current\_setting('app.current\_tenant\_id', TRUE), '')::UUID);

## **4\. Local Regulatory Compliance (Bangladesh VAT Act)**

The transactional engines in the Finance, Sales, Procurement, and Inventory modules must automatically populate and generate compliant reporting structures under National Board of Revenue (NBR) standards:

* **Mushak 4.3:** Input-Output Coefficient / Bill of Materials (BOM) declaration.  
* **Mushak 6.1:** Purchase Register book, updated in real-time as purchase invoices are booked.  
* **Mushak 6.2:** Sales Register book, populated dynamically on invoice finalization.  
* **Mushak 6.2.1:** Combined Purchase-Sales Ledger for trading entities.  
* **Mushak 6.3:** Tax Invoice, generated automatically in real-time at the point of sale/delivery.  
* **Mushak 6.5:** Branch-to-Branch Stock Transfer Challan.  
* **Mushak 6.6:** VAT Deducted at Source (VDS) withholding certificate.  
* **Mushak 6.7 / 6.8:** Sales Returns (Credit Notes) and Purchase Returns (Debit Notes).  
* **Mushak 9.1:** Data aggregation schemas optimized for monthly VAT Return submissions.

## **5\. Development Code Quality & Test Rules**

* **Request Validation:** All custom HTTP FormRequest files must extend the unified App\\Http\\Requests\\BaseRequest to enforce a standard validation failure output format across the entire system:  
  JSON  
  {  
    "success": false,  
    "statusCode": 422,  
    "message": "First validation error text.",  
    "data":  
  }

* **Test Isolation:** Every feature must include matching unit and integration test files (Pest/PHPUnit). Tests must run and pass before opening a Pull Request.  
* **Deterministic Logic:** Under no circumstances should Jules introduce third-party AI dependencies, client-side predictive models, or LLM wrappers. The application core must remain strictly rules-based and deterministic.

[image1]: <data:image/png;base64,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>

[image2]: <data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAHAAAAAaCAYAAABvj9h3AAAEdElEQVR4Xu2ZXYhVVRTHl1hhJEYoZqTMFFREjVmhopSClGRoBBZK+fEQqJkgBVr64IuID9qLZIEJw/Tiy+AH0YsIM/SQDz7oSxSmMIopJBqEChla6+faa9x333vu9x0n3D/4c8/sfc+55+z1uc+IZDKZTCaTgedVX6v2qc4HHQ5/oz2qhaoH/YT/ERPSgYRXVM9I8bONVT2hmqOanMyNGiap3lV9rrqt2qZ6L+gj1YDqX9UJP2GEYXGXqN5PJ6rwuGqX2HmVmKI6qOpTHVFdUW0o+Ybxm+qkmCPzObN0enSxSDWkejIZH6P6SsyIDyRznWaj6qKqVzU1mavET6pbqhvhs5IBH1Udk1KHXKr6J/obZql2ikUh4Eh/ql4e/sYo4hGxhyIKUzBav5gB8exO8pzqOzGjEf3NMl41KOUGHKf6QcxYryVzlIoPwzFlhah87O70HX5WXVI9nYzfc7ghbix9KCAih8QMSDR2Arx8rtgCkc6LalK9FBmQenZOdV31ajKH8+4Px5zH83KdmMEwTrZycIqJYmvDMb/Bp8M48yiFOWorDsNntzT57O9I5QjjBzaHudPJXDvgZpeLXfuotM9BigyI0TBekQGJThaf42oGXCs2xzFivEesAfxDrJfAGZapfg3j16TU8HBKdUD1iVjmOSvmAA2zQyrXuC1itYTaUk8NagSMR6rkxkmd7aRZAw6KnVvLgMzjbDRWf4s9x0D4Ttwz7FU9HMa7VRdUT4W/CZa0cSIDNWxAz/d4TS22inVkFH0v7t2qD1RdqhdVq6Q0hVSD2uuNCoZsFyNhQPCUjGFiB/cUHENwMOb3hCE9eterXgjjDUPh5kJDyXgKBqJOETm7VV+GcV8UrnFZtSCMNwLXpPYdV82W1lPpSBsQxZFTyYAQGxBuhjHXYmni2cnVnNyfTkTgPeRqjywennztxxiUfWNTBTgC41ELvZlpliID1mpi+sJxrSbGr9uqAWlsVoit/VWxdPx6NF8T2mT2Q1yYwlyE36hDuiBtAAuxLpprB6SXXrGtBGm2UYoM6NslysUbyRyO7GvwkuovsZccMTgt5YayA60YsEfKu1X6DbYzdbNJ7KK/S/VGgh86FD6B7YZvfDHg92JG3S4WQanntgKLjiE/TSeq0CX25mSllGcF7vMX1Y/RGJFPVMbpi86cJs7HfIHfHP6GyHSxrjNeP3qDNWLrGhuI52CMrptrsW44jTc5QC8QX78Q97A4/yLGi+DNBDWQH+Q1FeEOLNBD4RjDshBvhb9HmkEpfyZXHI0zVGfE6v9nYm9YeqN5YJF5o/OtWHngkwbNDYqTpr9BM5eOeWqOx1gjUidBMRTmef/8sTRRAxuB7QaaL7bxB1IBAm8SvMiPZnA8DIhxpiVzDjUKw68We6HfTjwz+OafbUVHoR741oFIowZ6rWEjzpsEN2C1epq5RxBVz4qFOLXuG7HulNTydvgOHs1Wop49Dcb3f1/Voy/stEwr8CqoW8rzNOlgntS/gc9kMplMJpPJ3G/8B4U6HI07Jl+1AAAAAElFTkSuQmCC>

[image3]: <data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAHAAAAAaCAYAAABvj9h3AAAE0klEQVR4Xu2ZW6hVRRjH/1FJUWI3upDiLqiQ7kZJkQZhUWIRlBgp9RB0hy7Q9aEXiR4siOgClYi99BKahC8ieCgoqIcSutEFTpFJRQmRQkXW9/Obzz1n3Mu99znreE40f/iz15qZNWvmu8/aUkVFRUVFRQWYZ3zJ+Irxu8S30j183ni18fB44D+AY4ynGI8oOwrMN56h5r0dKp/nUuOJRd+0wQnGG4yPGvcYnzTelHi7cavxH+OH8cBBBsK9zris7OiBjnHE+JlxjXw/bxiP6w7Zi5ON643rjBuNvxjvGzPC8ZXxI7kh83vx2O7phWuNo8ZTi/ZDjC/IlXhY0TfZuN/4g3GtcXbR1wufGB+Qew5YJV/3ZuPRqW2WcYvGGuSNxr+ye3CJ8Wl158KQdhov3DdiGuEo+abwwhIo7U25IE4q+trGWcbX5UrD+4fF3/J13pzu8Vzu8cTF8pC6Sa6sy9OYAKliRbomreCVx3a79+JT4w7j6UX7lIMFsbByUwCPHJULAm+cDGDll8kFRDhvykn9cKbxfHXXiUGy7s/lxkc++9a4y3hRGhNg7GvpOhQfXhsYSe1EqwBGcbz8nVyXuZd2+mEJ+sitGAy/HY1z79ert4fxgkdS35dFXxtgsXgLcxPm2jYQQipeSYgEKA3lNSkQ70T4ofgmBd4p7+Ma0n6uvAD8Se7xGMNy4xep/XeNVTz4WJ6j75VHnm/kBjA0nlLvHPe4XADvabAcNAxQHqGShRM628KRckGxJzzvSnUNo58CR+SK6adA+pmTwuoP+T62pjF5zfCifD2gY/zeeFq6x1nKwokINLQCI95jNf3whLwiw6IjuXeMtxjnGs8x3qr+5XuA3BuFCopsGyvlgqRgwQDbVCCIkIxicgOPEJwD56CNPoAiw3vvMZ6d2ocGiZuJRov2EiiIPIXnPGN8NrWHUJjjZ7nFDwvmJPe9b1yg9kIpQkW4rO05TZ4CYe45vRQIcgWCP1NbcKnGsXdiNQ9TaTYB6yFWh2exeeJ1XKNQzo3jSsAZUB65MIqZYYEAckGigBH5/iI8IewmBa5L1/2KmFDCRBVIYUOUQPa/ysPxwqy/LyiTCS9MTGJuQiw0EJYNEMRdWV8bILyslR8lCLODgPMd+9imbsU30/huakeBcVyKY0UODDlkQCX7m/wjRw6MlnRD2gETUSBFT1mtUm9wnBkYD8sn3a4DFxK8aEP6BRw34uCLAt+WK5WDc35obgMIHUU+WHYUQAA/yj+PBTAs9scRKYTOOilu3olBcs/HK/PwRWVOERdtIeCr9o2QzpNXnbn8qA3ukL83VxD7oI2qm7mQG0YTRQ6gFsjnb0RYGBPmpL0JfJkgB/LC1XJ3B4TNGekaxSKIa9L9wQbr2i3/lvuQfE8fyM+HOS4wfi3P/4zjCwsenwMhM9er8vTALwVaKBQjLeVHMVe2RWjO25ARoROnGE39rPlujSMHDgNKc3iF3KoBoQCCKBIiyU8FCJ+EKZSD4poEguExBuXMKfoCMddt8g/6bSLqhTj8c6yYVJAP4uiAp5EDo0jgIM6XhFDggfJpxRQBrwqLJte9LK9OCS1L0hgsmqPEIGcalB9/Xw3Cx/yxiomALxwd7R+WCAeLNPgBvqKioqKioqKi4v+GfwFAji9cbK7zLwAAAABJRU5ErkJggg==>

[image4]: <data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAHAAAAAaCAYAAABvj9h3AAAE20lEQVR4Xu2ZXcifYxzHv2toohYT1shDeWteNyMLQxhCa8hrHCg2lCjGDpQkB+xEWKHWnDjRkHaitT05sIMdzAFNXuohpsgWsTJ5+X38rp/n+l/+9//92fPI9alv//t/Xff/vq/7+l2/l+v+S5VKpVKpVOB008umV0xfJb2dvqMXTFeZDo4f/Ac43HRG+uzEItPJan622ab5pgtNRxd9M4ajTCtMa0x/mJ403ZR0j2mb6U/TjvjBAYbJvd50c9nRhkNNz5l2mzaafjK9YToyP8k41rRJfs47ph9MD7ac4Xxm2ilfyHwuae2eWVxjmjAtKNpnmV6UG/Ggom+qeUhujA2m44q+dqw3vSY3JGC4rab3NOmNc01b1LogbzT9ln2H803Pyr0QWEh7Tef+c8YM4jD5Q+GFJRjtTbkBjyn6Rs2pptflRsP7+wUjXFa0LZZ7Igt0jmmz/LyL8pPkqeKOdExawSuPmOz+m49N35pOKtqnHQbEwMqHAjxyQm5AvHEqYJUvlU8Q4bwpJ3UDw3xvulKTY73T9KnpeHk++9L0i9ywOSxevBcI2TxvmUPHUzuLIWBRzJPfj2PuwWdAO/2ohD5yKwuGzzEN+Ow3qL2HcYPHUh+TMGoY7K3yaxPmhl0gXIOxInLcctPXppWpH6NhvCYD4p1MPsedDHifvC/uRfuZ8gLwO3ktwWK4xfRJav9ZrYaHD+U5+gF55PlCvgD65hm1z3FPmH43faDeclA/YDxCJQMndI4Cxkhui4klquSG6mbAcblhuhmQfhYbhdWv8ufYls7Ja4aXNJmPx+SL6cT0HWcpCyciUN8GjHjPqunGWnlFRtKP5D5mut10grx0v0utIaQT5N4oVDDksDxv+lz+TBgzDBnFxygNCBGSMUy+wCME5+ActNEHGDK8937TwtTeNyRuLjRRtJdgIPIUnsNErUvtMSlcg/xzeWrvB65J7ttuukCDh1KKFX4f3Gb6UT42QtVUGRDlntPOgJAbEPanttB1GuDZidX8mEqzCVYPExCexcMTr+MYg7JvHCgBZzD55LEoZvqB7QHPUKaB0+QeHuGJyW4y4MZ03K2ICSMMa0AKG4osxr1HHo4vzvq7QpkcOYPE3EQMNCBcEDaAiViV9Y0CwssG+VaCMNsLjJEQ3w4MgwFju0S6uKLlDF/IMQdny72Zlxw5LFrSDSEahjEgRU9ZrVJvsJ3pmUflF/1GnQsJbvRW+gS2G7HxxYDvyo36tFo3zaOASceQD5cdbWBMFF6Rn5kUKkGKGTbmwDh3md5P3wHPxyvz8EVlzrWiLSaYLUpwlrzqzOePe98rn9fcQDwHbVTdXIt5Y9FEkQNEivz6jcQKy+Mvor0JJoAcGK+rcHcgbB6SjjEsE3F1+n6goYAir3xkekr++ovXYUvyk4xz5MUO+f8ReZGDx+cwyftMr8rTA59cPwzKIi3nj2KubIvQnLcxR4ROnGIi9fP+ebUGyIH9wHYDLZOvaiAUIIgiIZL8dMCqv1RunPPUnJdp5xyMwya/HeQoQt7d8hf6oyTGFZt/thVTCvkgQhOeRg5kFY7LN+K8SQgDdsqnlWkCrzpF7uLkuvXyio/Qcm06hxXNVqKXPQ3Gj7+vetHj/rPKMPAqaEz/jtOEg0vU+wa+UqlUKpVKpfJ/4y+8GzI4bsa2ZQAAAABJRU5ErkJggg==>

[image5]: <data:image/png;base64,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>

[image6]: <data:image/png;base64,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RERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERGRX/hfsZYMvHVjt3gAAAABJRU5ErkJggg==>

[image7]: <data:image/png;base64,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>

[image8]: <data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAEkAAAAZCAYAAAB9/QMrAAACXklEQVR4Xu2XT0hVQRTGT4hQKFgYheDGlWsJEdwGkYtACiFw2aJwE62kNi41xE1LCaKglasWgkjQAxcuXLhJgiDIEMGNkSCIUfl9b+Zwj3Pv7d377iPxNT/4eM6fe2fuN3POjCKRSCTy/3EJuhDUrUHfoH2odrrpfNMJ3YEmwoYc2P8mtAl1B21j0Gvoj7SBSb3QPPTc/12U65KYSiNCk8gN6FDOsUmD0BtoF3oQtJWhLU3qgEahLWhc3G6oQhmTLkuyA7Pg3K5BfZK/qy+KawtzIN8donNiW9b8UnBi96HP0KqkB2mWoiZ9kCSRH0NTSbc6NGgPegctistzw6adxnEczXEcb9rUUUTHZHkOeitu3F+Svzh1ePrsSPm8U4QiJn0P6p9J8lHkLvRb0gv3yavfl63pdjw1y8J5MVquQl3QU0m/PwU7PhaXg5iLWkURk3gdsNiPYvgsm7KlJq5+0pfLmsRd1NCYLLjlmIvWoRFp8iWGIibVgnr7UcxRX0zZwo9k/QtfLmsSryCVoEHMTZrAm6WqScw126Zs0XvWS1/+5yYpA9ArcdcAhmRZqppUJNwe+nKeSWGOIy01yUKTaNaTsCEHhu4jcRPU5KowjG9DP8WFtsJnFsQ9o6cNf3mi3dJOvo6nIJO6wtBkIt+Arvi6e9CRuPf1+DqelMxjS+IW4czgpLJUE7fKGkIqrmp4ZFNccUJTeAX4Cq1AB9CQb7Pw350f0EfoPTQLzUjyPi6wXgFUnAvDui3g7uMVhR+UFb6KXjq1D3/5TNVDKNIIOkyni+pvqxiJRCKRyNlyAqRqpqfUpYGqAAAAAElFTkSuQmCC>

[image9]: <data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAHEAAAAZCAYAAAAG2cHnAAAD/UlEQVR4Xu2YS6iVVRTH/5GBoZWYpkVhgZaCUpAPiCArBzUIJIjCkdBEQhAFFQTBigYRgkUQCBHVoJdQEIFFgy+Sgho0yUkUaFRCk2iQUNFj/Vzf4qy773e85+X1ivsHf845a+9vv9Zea+/vSJVKpVKpVGaTq01XlEbjWtOC0mgsLQ1jcKXpXtOdZcHlylWmR0yPlQV9oP6Dpm9MC4sy2G/6z/S96ajpHdPPpu9ypTF5Ut7HP2XBBYD5Llb3hr2oXG96wfR8+31QlqnndBaxnxO/Nr1ketp0uya/ALMZiVtMn6h7rheFO0xvmH6R7+ZRmcmJr5fGS5iXTY265zqrsHPvMZ00bZVH0zhMwolE5/z0nYxQRuyN6tUpiWdKFqXvjK/fXLEvKY2a+jyQsht1zxViHIz1hqIMeG6N6Wb5XEjNQ8FAn5CfR6SEcpFGZRAnMinOxIOmW1J5PIvYUDtNP5h+lJ+dD5iWm96SZ4x/TW+bruNh9c7cENDX6fY3fW+Qp3Ta/Mv0lHpzb9p6iGcgjyk2IA75MtlD9JXh7P/W9Kq8vftSGfP4zLTP9JzcD/Q1MNwef9Lw594gnM+Jj5o+VW/R2Ujva/olBGccl48z+FzeLo4Noi/azLDY4cTgbtNvprXJdkBeb0+yhdPDiQF9hRODP9QdiTgZ5+W+gL4Oyes37WdmKCcCV/1d8h3NWTgpzufELiJ6ssOwscCZRr5oOCPgAvN3W5bp50Q2wjXJFn3zGUzCiQ/L210tby/0p+mYPHWSTYhUonCzprcxFEQDqYv0sEnjp9VhnUh0Up9zIWBR88JCo+lO5HssZKafExtNHdeFcmK0+5r82MjaK3fiirZOVt7II4EDORvjgjMq/Zy4Tt42qS+n8KifnXOpOjE+Sc9da1DCGf+iPCKp/6zGD6Jz3CbfQbxmdP27MhP9nBgLxvm3OdljwsuSbS46kYwxkxOjHd4fuXSVF52AdX2lsHFLZZN33YrHgs5w5u6yoA+k5h3yhcnpEbjMfGR6XL3dtlF+2cj/2NDGYdMz7feAf4HOmu6XP48ekp+JHAUBY/5QPoboJ+pSL28W+qFe7ouUxqXqTFSSj/NX+Zl6U7Kz6NhXmeZp6uZcL5/XymTjEkdbOL0pyrAd0YQicVRYjC416u1UHPuVfDHek1/xvzDd2pZHFGdtk7eRbUQJUZBtRElEVq53V/s5U10U8AwpjleDD0xvmra3dVBEHBeY3+XtM4/yvfNdeeahrRPy9Amsx8fy1ybaZzyn1Lu1z3mYKLuUd1RSCH82zEWICNJhvOBzGeHVoRwv8yG6yz8CAhxWvuzTNrdk2sJOeXn8VC4HYpcNqrpLKpVKpVKpVCqT539q8hd7j4TmaQAAAABJRU5ErkJggg==>

[image10]: <data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAB0AAAAZCAYAAADNAiUZAAABe0lEQVR4Xu2UvytGURjHH2FQUn4MhIHNRCGLJBkYSBSDwWpQBmE2+AdkkB+L2aAUi8Eok5QMNvkxiKJY5Mf323POvfd96O3lnkn3U5/Oe57n3Pu8PeecK5Lx3xmE63ALXsFT95sxOguro9WBWYGbNggG4Ae8hI0ml4pyeAQnTZy0wxf4KdqVYDTDO9hqE2BUtOAb7Da5VAyLvrjSxIvgqsudSMC99a3li5MUwwv4JIHbSnxrWZSnl17Dd7gAK+Kl4fCtPbcJQ5/oSa5zI+eEW9AlevppQSyLFt2xCcOi6Dp6CGtdfAwewCon53nxrX2FnSZnmRa9PpYbyX32Vn6+BRHJ1taYnIVFp+A27BFtK+EdTv4ZzocS829siBbdFz3F+ZiHS6LX5hjOufivixZKKeyVuBvcX1/s2Y2eR9ifmP8Z/ylcc3MWfYAt8F5y77GPp6YJ7sJ6WCJ60vdgmRtn4qVRPAgjop9EngPuaYOLd8AzOA4nYJuLZ2SE5ws3BU58HObtPQAAAABJRU5ErkJggg==>

[image11]: <data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAB0AAAAZCAYAAADNAiUZAAABhElEQVR4Xu2UPShHURjGH7EoKTEQFilRMsgmSQYGEjaDyUcWgzAbZMcgH4vZoBQ2kslkkcEmJYPBwCIfz9N7jnud/P/oXovur37de9/33HPufe97LpDx3+ml63SLXtNzd66YnKblH6NTZpluhkHSQ1/pFa0Ncokoocd0JIiLVvpI32BVSY06ektbwgQZhC34TNuDXCL6YROXBfECuupyZ0jx2/rSauI4hfSSPiDlsgpfWi2q7pU39IXO0dJoaHr40l6EiS9ognW5jp4uWIdXOXX9LYuwRXfCRMAQPaA19JR2u/g87H5vpYvnxJf2ibYFuTh9sAl1FOroe9pIJ2Hb6sfES1sR5OKEi2o/+y2kRUfpBt2GdXxeNFCT7cO6OBcN9I5OuGv/SfQQs3SBFsO21Iwbkwp6gw46TscQlbcTn6ukv9evyp2PZlrvzvWmh7C30yJrfhCih0mM/4Hswkp4AusHoVi1Oy+ie7CHSYwmW6FL9IhOIWqYAdivchjWI9pSGRl/wztXn06ugS6TWgAAAABJRU5ErkJggg==>

[image12]: <data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAB0AAAAZCAYAAADNAiUZAAABYklEQVR4Xu2UPS8EURSGjxCJRDbxUdKIZEMiCtGJSkFBYUs1Eo3OPxA9ovDxDxQqdERUKo0odCIRhUJBIz7eN+cee3OykyX3VjJP8mRm3zM7Z/bsvSNS8t+ZgbvwAN7D63DOjK7Cnp+rM7MJ930IpuEnvIP9rpZEJzyHCy4nY/AVfolOJRsD8BGO+gKYF234DidcLYk50Rt3ubwFbofalWT8b220vHFMK7yFL5J5rMRGy6ZcvfQBfsA1WKlfmg8b7Y0vNGBYdJXz2Cinv2JdtOmhLzhq8AT2wUs45fLuULO8EBvtGxx3tZhZ0QfjkXBFP8MhlxPLC4lH2+tqMb4p97NtId+06dbaE/3SsegqLqIKn+By+Gx/CZvFOfEPkQT37CRcgotSH6PlO6HWdLx/YQQOhnP+0lPY4XJieTL2AjkSfStdiK4HYnl7qFmeTBvcghvwDK6IjpVYzsXFmuUlJfn5BiMaUCAQIUG8AAAAAElFTkSuQmCC>

[image13]: <data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAkAAAAaCAYAAABl03YlAAAAjElEQVR4XmNgGAXkAkYgFoZiDMAJxLlA/AyKnwCxLYoKIDgExFeAWAXKNwHiPUDMDVOQA8RTGCBWwYA8EPPBOIJAfBqIPeDSWIA+EH8CYiV0CWRgDMRfgZgHXQIZgNwxgQHhYJiYExB7I4kx8APxeyBeCMQrgfghEM9FVgADIOskgVgciFnR5EYBEQAAAK8QUyUGpugAAAAASUVORK5CYII=>

[image14]: <data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAcAAAAeCAYAAADgiwSAAAAAo0lEQVR4XmNgGOpABV0ABhSBeCG6IAy0ArELuiAIcADxViCWRhbMA+L/aPgnEFuCJHmAWBKII4D4H5QtBsTMIEkYKGeA6MIALEC8Boifo0uAgDgQ3wXiA2jiYGADxL+BeBK6BAgUMUDsC2KAWAFynDBIAmbfWyDWBGJjIF4MxJxgbQyQIHvIAPHGKiA2g0mAgC8QfwTiDUDsiSwBA7DAGAXIAAD8ORoJ0Ewr5QAAAABJRU5ErkJggg==>

[image15]: <data:image/png;base64,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>

[image16]: <data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAIoAAAAZCAYAAADudbaJAAAFCUlEQVR4Xu2ZbejdYxjHv7IVGXmKKcvf45LHxZSnlEYUWqitEK8IL5Q32mut5o2E0hiakiihpSTZCcm2MoqtRCEPodEWasy4Pq772rnPvfM7v/N/2Dl/f/e3vp1z3/fv3E/X976u6/4dqaKioqKiYtbgIOMliXyvqOiLs4w/JfJ9tgHxXmE80Xhw0RY4O/GQsmGO4jjj18afjR3jgp7WA4THjX8nPlK0jRtLjduN641bje/JBRM4zPio8cXEHcaHsva5ikONz8ht1tEIhDJPLo4Ljb/JB54N4eci4y/GdfI5shEd+fwQDbjYuNu4OpUDCIe2uY4L5DbraARCYbDT5MbAKBgCFz5ubDDuNS7L6i41Pmw8I5URBPO9Zd8Tjru0v3jmIkYmlBBHeBAEwikmFI3bq7AB36g3zOTIPcz1vU3/lt+Sh6UmkOvQ9+nGY+SHpVzzkcYTjMcX9QH6IFfgmabciHramwxJH8ca56fv9Me4TeA55kN/TUJhHfQDsfGE/HdTBgN9mpUZAJEglnF7FQSwxbjE+IpxlzxEHpHa2fyv0nP9hMK6MEA/sM7PjWuN98r736jezd4s7/9p4yfGFeoV0rWp/Vt5KPxLnhuFQY42PmX8LrXz+UKqBw+omxdibLwlySmHg77eT8/leEnexnw+NK4y/q5eoZD0fyQf6zn5BeUL+X5NCcRwksSTi3o2IyaPkIbBcuPNk2QbmAOh55qsjhATCXebUGhr2hzaryvqEAubTZJI2MvHBX/KN58Tys0Qg+Z7h2BItBmTZ8q5A8rUP5HVISLWwAEN4OnoPzwZ4kMkZX+lR4nyfeqKmj7wrk17MRAshEWj6n5AhUw+EslxgPHL0HOZ3GDfa3pC4ZBwMl823m48NWs7T+69eIbfBxkTD3eUfN9eV3O4OVP7zx2EIekrEEK5MauLtcX8Y06n7HvCUQoF4TIu/VGHoKcVctgErpqLyoYETuxkvcpMg/HfNR6e1cXGcCqnI5TwmjmXpjZ+S/l545MF18jzB4wLmxBhqRw/PAXzD4RQ8jWUQok5lf2VQgEI7g9117VT7j3Dw0wKg7wJiBdwDIRXGQfiVOR5Q2wMbW3JbEfNCSTgkHA7wi3/Ks8hSGgxcj+j5GgTSpNQc3EHZloogOT8VvnLOH6323h51j40BnkTgPoelA/CRNrACQkFD8s2MG6ZkHLqSd5wwyCu8xg8x/3yq3MTlhVl3HP0Q9jYocGelEM26FZFH8yRkJEjDEv/gWGEEr9rCz3nGB9TNyRixzvkYXbQfvQFyiqN1kZC1ahxg3yBq+QLxpjPym9kvIwDbM6b8qw+MJHKJKVNwCj0m/8dQDIaf1+cJB+H/74CiPKmrMzc3jEuTGXmtFF+JQWfGT/I2vmkzI2F/gFC2yDf4/xd0LnGH42Lszr6f1vdWxNgfH7LermlhnCYa6z/fLm3vCqVh0IksaUQ2hjZ/iiBOO6RexBuJOQrGI/QkIO4z1XyzkSuvZz2QUAobxi3ya+/GKDslxO4Ry6gjvE29cb5K+W3RgSzSW60iawdYdA386edz7Xqfz0ORsjK6zrqhhWu7OQceKHX5B4wQjFEKB/L9+tL46vGH4x3a4o5yn8JxFs28Go1u3o8AydypfytbdumhGvGALj38AIl4mVZ0ws3xmF+Ze6QI16QTev2kSFeAvIZfTN/9oBy7BFzn8lxKyoqKioqKioqKioqKir+T/gHu+dqKr8aYqYAAAAASUVORK5CYII=>

[image17]: <data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAA8AAAAbCAYAAACjkdXHAAAA90lEQVR4XmNgGAUjDfCg8QWwiGEFQUB8EYhXA7E+EB8C4hNA/AyIGZHUYQB+IF4DxCpA/B+IXwOxFRBbA/FHBgK2uwDxLAaIDSDNGVDxXiifBcrHCjyB2A6IeYH4ORArQcX5gNgepogBYngAEDsjicGBJhBvBWIOdAko4AbiXUBcgy4BApMYIK4gGYAC5QADwsnIAOTcVCDeDsRbGLAEIEgTyL/YnKwIxFlAbALEV4FYHlWagcEBiP+iC0KBLgMkPMoZIFGKEfrMQCyMLogEYN6KRhMnChQxQGxVBeIyNDmCIAyINwPxBAYsfiYGgJIxK7rgKCABAAByHR8zRgDRlwAAAABJRU5ErkJggg==>

[image18]: <data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAEsAAAAZCAYAAAB5CNMWAAADl0lEQVR4Xu2YS6hOURTHl1CEPPMWVx6JCEnkUaIYkMdEMTIxEUkoGZiYi0xEojzyikJ5DIgyMSEikkseGaAUJYX1s8/yrbPvOec73/1Quudf/76zH9//7rP22v+17ydSoUKF9qGrcpCyZzzg0EnZK/ns0HiuPKe8p1wWjRlWKS9KcUD/OropJyjHSvFC+iinSciCRlGkP1y5IXkepXyiHPF7NKCH8q1yatSfi/7KIXFnk+is/CJhxy4oP0nbYPRTnlJ+VB5RPlXOT83IB/obJa2/NTVDZL1yumvvVl5Rvla+VL6SEMAtbk4p8McXKO8q70hz53eW8qsEHzCQZSyuxfX9UO5wbbLkvfKEsovrj2H6t1wf+pckrc8G+CRY6p4Bmec1GgZBmikhaASPIDYKdpBAxC9Mn/lG36S9pjb868VeKFuVw1x/DNM/E/UTHO9LByUExLDSPbO2/dKOrMrCZOVVCWm6OhqrBxbNy8Sgb2/yTGBo+922YH1XLnT9MUyfz7jf9AHrnpE8kwT73BhB/eOmPl5CNVmn7B6N5aEoWMckLLwoWPT7LIhRFCzTB3jxAQleOVt5Oem341fa1BvFTgmeQ9rGRh0DU/0sac/irsML3pDabsaetVhCVtG/3fXHMH3vN+g/krR+FuJrAglAgXkmwXaaArvN2d4kocyWATu7TcLCrL1ZQhAwYcwYXJdwzEcm7ZPKd8k8KlkeTP+ba6P/UNL6WbgttYzie3uUvZVjlDeVE5OxhtCiPKx8I+EINgoKA+Wc71OmySCCgOkauDpQ+bhiMAezZ4eZt8TNywL63KG8/lFJ68fgRHhDnyQhqwzo7XLtUsCn7iuXS/0jVwReaIDUNPIyhkywux6exfWBa0QZeH08K0vfQKb7I8rmeG/leB937VyQklYBuTa058pgQGuKpDXwr/ietUhC+huoXGRZvXuW6c9xfejjYV7fg6POEfTAF32wRkv424UYKiFQVkWaBdWGwFh24An4E58eLPSs1KrsBwle5NdBtWQetMpp+vybAkwfZoGMOi1tqznXEwqKgesEHvZPYQZ8KCFHK+tFrknIBso7L0PFjY89GfFA+Vg5Lukz/VZJ6w9Oxj34fp5xcyQ5SegR8PPKuakZ/wgsgEshvsBLZmUtgZknYc6KaKwe0EO3SB9QydfGnQ7YAFcJfpWIs7rDAS+r578DJfzyUQpEk0pUhqVFK1SoUKFChf8CPwEWRLVnbZlcXQAAAABJRU5ErkJggg==>