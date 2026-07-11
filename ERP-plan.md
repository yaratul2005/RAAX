# **Comprehensive Architectural Specification and Implementation Master Plan for the RAAX ERP System**

The RAAX Enterprise Resource Planning platform is designed to serve as the definitive system of record and operational orchestration layer for the enterprise, built to scale cleanly to support multi-entity and multi-branch operations. Operating on an API-first, security-centric foundation, the system enables standardized, predictable processing for 1,200+ employees with zero client-side artificial intelligence interfaces. Through a modular monolithic architecture, RAAX enforces complete transactional auditability, strict division of duties, and frictionless compliance monitoring over its multi-decade operational lifespan.

## **1\. Context and Goals**

### **1.1 Vision and Objectives**

The RAAX Enterprise Resource Planning platform establishes a single, cohesive transactional registry for a growing organization of 1,200+ employees. The core objective of RAAX is to consolidate fragmented operational datasets into an integrated, real-time data plane, eliminating reconciliation latencies, manual workflow handoffs, and data synchronization gaps. By utilizing a highly modular, decoupled architecture, the platform scales alongside the enterprise without incurring technical debt or system degradation.

\+------------------------------------------------------------------------+  
| Year 1: Stabilization & Ledger Integrity                               |  
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
* **Year 3 (Operational Optimization and Compliance Automation):** Achieve complete automation of the order-to-cash and procure-to-pay lifecycles. RAAX will integrate automated tax engines designed to handle localized, statutory calculations (such as sales tax, Value Added Tax (VAT), and withholding taxes) directly within transactional workflows1. Automated generation of tax ledgers, tax invoices, and monthly returns reduces manual audit preparation times by 70%1.  
* **Year 5 (Holding Company and Transnational Scaling):** Scale the RAAX architecture to support multi-entity holding company configurations, localized multi-currency accounting, and decentralized international branch operations. By utilizing database-level multi-tenancy rules and Row-Level Security (RLS) policies3, RAAX will support distributed multi-country entities on a unified infrastructure footprint with zero risk of cross-tenant data leakage4.

### **1.2 Scope and Boundaries**

The delivery lifecycle for RAAX is partitioned into logical development phases to guarantee focus, mitigate implementation risks, and ensure project tracking.

| Functional Area | Phase 1 (Core MVP) | Phase 2 (Extended ERP) | Phase 3 (MRP & Assets) | Future Roadmap |
| :---- | :---- | :---- | :---- | :---- |
| **System Core** | Single-database structure, OIDC authentication, RBAC, append-only logs | Dynamic feature flags, localized system settings, event-driven integrations7 | Dynamic form and field customization tools | Global multi-tenant database partitions |
| **Finance** | Dual-entry Chart of Accounts, General Ledger, Journals, AP/AR aging | Localized tax/VAT logic engines, automated invoices1 | MT940 bank reconciliation integrations, asset registers | Multi-entity cash flow consolidations |
| **HR & Payroll** | Employee master directory, roles, attendance, shift scheduling | Leave workflow approvals, basic payroll engine, benefit matrices | Appraisal workflows, KPI analytics, training management | International payroll localization |
| **Procurement** | Supplier register, Purchase Requests, Purchase Orders, basic GRN | Multi-tier PO approvals, item-to-supplier price matrices | Automated vendor scoring, dynamic reorder triggers | Automated multi-tenant supply networks |
| **Inventory** | Standard stock tracking, multi-warehouse support, FIFO valuation | Bin location matrices, barcode registration, stock corrections | Stock transfers with integrated transfer invoices2 | Global intercompany inventory routing |
| **Sales** | Customer master records, Sales Orders, basic invoicing | Dynamic sales pricing rules, credit terms, automated tax invoices2 | Service level invoice processing, credit notes2 | EDI-based sales pipeline integrations |
| **Manufacturing** | *Deferred / Out of Scope* | *Deferred / Out of Scope* | Bill of Materials (BOM), work orders, routing configurations2 | Shop floor data capture integrations |

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
| **Compliance Officers** | Internal & External Financial Auditors | Audit Portal | Read-only access to append-only database transaction logs, change histories, and statutory tax registers1. |

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
  * **PostgreSQL Row-Level Security (RLS) Multi-Tenancy:** Automated separation of multi-entity databases using engine-level isolation rules to prevent data contamination3.  
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
| **Core Workflows** | Journal entries, Accounts Payable (AP) and Accounts Receivable (AR) management, and multi-rate VAT calculations1. |
| **Critical Validations** | Ensure double-entry balances match exactly (![][image1]) on every journal write transaction; block adjustments to closed fiscal periods. |
| **Required Reports** | Real-time Balance Sheets, Profit & Loss Statements, Monthly VAT Returns (e.g., Mushak 9.1 data layouts), and AP/AR Aging Profiles1. |

To support statutory localizations, such as Bangladesh VAT compliance, the Finance engine integrates specialized compliance reporting models1:

* **Sales VAT Invoicing:** Generates automated tax invoices (such as Mushak 6.3) at the point of sale to ensure compliance with transport requirements1.  
* **Purchase Register Automatons:** Auto-generates standard purchase registers (such as Mushak 6.1) to support input tax credit claims1.  
* **VAT Deducted at Source (VDS):** Manages VDS certificate generation (such as Mushak 6.6) when withholding tax from suppliers2.

### **2.4 Procurement & Inventory**

This module bridges physical inventory tracking with corporate accounting ledgers.

| Component | Detail Specification |
| :---- | :---- |
| **Key Entities** | VendorProfile, PurchaseRequest, PurchaseOrder, GoodsReceivedNote, StockItem, WarehouseBin |
| **Core Workflows** | Sourcing requests, purchase order approvals, receipt validation via GRN, and real-time inventory tracking. |
| **Critical Validations** | Reject GRN inputs if quantity received exceeds the remaining balance on the source PO by more than a 10% tolerance threshold. |
| **Required Reports** | Real-Time Stock Valuations (FIFO and Weighted Average), Reorder Alert Lists, Purchase Order Status Trackers, and Supplier Invoicing Reports2. |

### **2.5 Sales & Distribution**

The Sales module manages customer order lifecycles and outbound transactional billing.

| Component | Detail Specification |
| :---- | :---- |
| **Key Entities** | CustomerProfile, Quotation, SalesOrder, DeliveryNote, SalesInvoice, CreditNote |
| **Core Workflows** | Customer quotations, order processing, delivery note creation, invoice generation, and sales return handling2. |
| **Critical Validations** | Block sales order confirmations if the customer's total outstanding balance exceeds their approved credit limit. |
| **Required Reports** | Daily Billing Registers, Invoice Aging Reports, Customer Credit Logs, and Product Profitability Dashboards2. |

### **2.6 Production / Operations**

* *Phase 3 In-Scope:* Defers full Manufacturing Resource Planning (MRP) to Phase 3, but builds database schemas to natively support these future domains.  
* **Must Have (Phase 3):** Bill of Materials (BOM) management, production work order scheduling, routing step configurations, and basic shop floor data tracking2.  
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
  * Read queries for typical UI screens (excluding heavy reporting dashboards) must achieve:  
    ![][image2]  
  * Write and transactional update transactions must complete within:  
    ![][image3]  
  * Heavy analytics dashboards or consolidated reports must render in under:  
    ![][image4]  
* **Batch Job Windows:** Long-running system processes (such as payroll calculation steps, stock valuation sweeps, and ledger closing routines) must execute during off-peak hours (01:00 to 03:00) and complete within:  
  ![][image5]

### **3.2 Scalability**

* **Horizontal Application Scaling:** Web application pods must run stateless inside containers, scaling dynamically based on average CPU and memory metrics:  
  ![][image6]  
* **Database Scaling:** Primary-replica database configurations are used. All write transactions route to the primary write instance, while read queries are load-balanced across multiple read-replicas.  
* **Data Growth Resilience:** Database schemas are structured to manage data expansion of ![][image7] per month. Large-volume log and audit tables utilize PostgreSQL partitioning based on timestamp ranges, keeping active queries fast as database size grows.

### **3.3 Reliability & Availability**

* **System Availability:** Target system availability is set at ![][image8] uptime over any calendar year, allowing a maximum unplanned outage limit of:  
  ![][image9]  
* **Recovery Targets:**  
  * **Recovery Time Objective (RTO):** ![][image10] to restore complete operational capabilities after an infrastructure outage.  
  * **Recovery Point Objective (RPO):** ![][image11] of potential data loss liability, secured via continuous WAL stream replication to geographically isolated targets.  
* **Failover Design:** Automated health checks at the load balancer level automatically route traffic to the disaster recovery site if the primary infrastructure zone becomes unresponsive.

### **3.4 Security**

* **Authentication & MFA:** OpenID Connect (OIDC) authentication is required for all users, integrated with enterprise identity directories. Accounts with finance, payroll, or administrative permissions must use hardware token Multi-Factor Authentication (MFA).  
* **Data Encryption:** All data in transit must use TLS 1.3 encryption. Data at rest must be encrypted using AES-256-GCM at the storage and database layer.  
* **Secrets Isolation:** Application secrets, database credentials, and external API keys must be stored in secure systems such as AWS Secrets Manager or HashiCorp Vault. Sensitive keys are never committed to the code repository.  
* **Compliance & Privacy:** To support data protection guidelines, RAAX provides automated tools to mask, archive, or permanently purge PII datasets based on configurable retention schedules.  
* **Patching and Vulnerabilities:** Automated vulnerability scanning runs weekly on all container images and code libraries. Security patches for core dependencies are deployed during scheduled weekly maintenance windows.

### **3.5 Maintainability & Extensibility**

* **Domain Isolation Rules:** Core application domains are decoupled. Modules must interact only through defined service interfaces or asynchronous domain events to prevent circular dependencies7.  
* **API Stability:** API endpoints use semantic versioning (such as /api/v1/). Deprecated endpoints must be supported for at least two major release cycles before removal.  
* **Code Quality Standards:** Developers must follow standard PSR-12 style rules. Code coverage by automated tests is checked during integration and must remain above ![][image12] to merge new updates.

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
* **Application Metrics:** System dashboards track core performance metrics, including request latencies (![][image13], ![][image14], ![][image15]), database connection counts, Redis memory usage, queue latency, and transaction error rates.

## **4\. Architecture & Technology Strategy**

### **4.1 High-Level Architecture**

To support a growing organization of 1,200+ employees, RAAX is designed as a **Modular Monolith**7. This architecture provides clean logical separation while avoiding the operational complexity of distributed microservices8.

\+-------------------------------------------------------------+  
|                        Inertia/Vue SPA                      |  
\+------------------------------+------------------------------+  
                               | HTTPS / JSON  
\+------------------------------v------------------------------+  
|                         Nginx / TLS                         |  
\+------------------------------+------------------------------+  
                               | In-Memory Socket  
\+------------------------------v------------------------------+  
|                         Laravel Host                        |  
|                                                              |  
|   \+---------------------+         \+---------------------+    |  
|   |   Modules\\Finance   |         |     Modules\\HR      |    |  
|   |  \- Controllers      |         |  \- Controllers      |    |  
|   |  \- Service Logic    |         |  \- Service Logic    |    |  
|   |  \- Repositories     |         |  \- Repositories     |    |  
|   \+----------+----------+         \+----------+----------+    |  
|              |                               |               |  
|              \+---------------+---------------+               |  
|                              |                               |  
|                       AppServiceProvider                     |  
\+------------------------------+------------------------------+  
                               | SQL Connect  
\+------------------------------v------------------------------+  
|                     PostgreSQL Multi-AZ                     |  
|                   (Row-Level Security)                      |  
\+-------------------------------------------------------------+

#### **Decision: Modular Monolith vs. Microservices**

7

* **Decision:** Implement a single deployable code repository organized into independent modules (e.g., Modules\\Finance, Modules\\HR, Modules\\Procurement) with strict domain boundaries7.  
* **Rationale:** Microservices add significant overhead, including network latency, complex distributed data management, and orchestration challenges7. A modular monolith groups code by domain, running within a single process and database7. This approach maintains a simple deployment pipeline while allowing individual modules to be split into separate services in the future if scaling needs change7.

### **4.2 Technology Stack**

The core technologies for RAAX are selected to balance operational reliability, modern capabilities, and developer productivity:

| Technology Layer | Selected Component | Architectural Rationale |
| :---- | :---- | :---- |
| **Backend Framework** | PHP 8.3+ with Laravel 11/12 | Provides robust dependency injection, simple routing, database migration tools, and an active ecosystem for enterprise applications7. |
| **Frontend Framework** | Vue.js 3 with Inertia.js | Delivers a responsive Single Page Application (SPA) experience. Inertia.js links server-side controllers directly with frontend views, reducing API state management complexity. |
| **Primary Database** | PostgreSQL 16 | Relational database engine supporting robust ACID transactions, native JSONB structures, and advanced Row-Level Security (RLS) policies1. |
| **In-Memory Cache** | Redis 7.2 | High-throughput cache used for session storage, query caching, dynamic rate-limiting counters, and background task management13. |
| **Message Broker** | Redis Queues (Horizon) | Manages background jobs and asynchronous tasks. Provides visual monitoring of queue health and failed tasks. |
| **Search Index Engine** | OpenSearch 2.12 | Offloads complex search and reporting queries from the primary database, providing fast text indexing and operational search capabilities. |

### **4.3 API Strategy**

RAAX is built on an API-first design. All user interfaces and external integrations use the same underlying REST API endpoints, ensuring consistency and simplifying system audits.

* **Endpoint Design:** API resources follow consistent REST patterns and require JSON payloads.  
* **Version Management:** URI routing enforces semantic versioning: /api/v1/procurement/purchase-orders.  
* **Authorization:** API connections are secured using OAuth 2.0 / JWT tokens generated via Laravel Passport.  
* **Rate Limiting Mechanics:** A Redis-backed sliding-window counter rate-limits requests at the API gateway to prevent resource exhaustion15. The default user limit is set to:  
  ![][image16]  
  The mathematical evaluation for each client connection ![][image17] at time ![][image18] is defined as:  
  ![][image19]  
  Where ![][image20] and ![][image21] represents incoming requests. Connections that exceed this threshold receive an HTTP 429 Too Many Requests response with standard rate-limit headers:  
  HTTP  
  X-RateLimit-Limit: 120  
  X-RateLimit-Remaining: 0  
  X-RateLimit-Reset: 1711902781

### **4.4 Integration Points**

To maintain system stability, external integrations use decoupled, asynchronous patterns rather than blocking direct calls:

* **External Payment Gateways:** Integrations utilize webhook notifications and queue-based background processes to reconcile payments without blocking user workflows.  
* **Tax Authorities & E-Invoicing:** Transactions automatically queue compliance data payloads. These are processed asynchronously and sent to official servers (such as NBR APIs) to avoid performance issues during high-volume sales1.  
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
|  \- Run PHPStan Static Analysis \[cite: 7\]                   |  
|  \- Execute Automated Test Suite (Unit/Integration) \[cite: 7\]|  
|  \- Verify Code Standard (Pint)                |  
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
|  \- Apply database schema updates in isolation|  
|  \- Update Kubernetes Deployment Pod Images                   |  
\+-------------------------------------------------------------+

* **Automated Testing Requirements:** The CI pipeline runs dynamic validation suites on every pull request, executing static code analysis, style formatting, and the full test suite7:  
  Bash  
  composer install \--no-interaction \--prefer-dist  
  vendor/bin/pint \--test  
  vendor/bin/phpstan analyse \--level=8 \[cite: 7\]  
  php artisan test \--parallel

* **Docker Containerization:** Successful builds are packaged as Docker container images and pushed to the AWS Elastic Container Registry (ECR) tagged with the unique Git commit SHA.  
* **Database Migrations:** DB migrations are verified in isolation before updating application pods8:  
  Bash  
  kubectl exec \-it deployment/raax-migration-job \-- php artisan migrate \--force  
  \`\`\` \[cite: 8, 13\]

* **Zero-Downtime Deployments:** Deployments run in rolling updates on the AWS EKS Kubernetes cluster, updating container pods with the target image tag without interrupting active users.

### **5.4 Infrastructure & Hosting**

RAAX runs on AWS, with all cloud assets configured as infrastructure-as-code (IaC) using Terraform.

* **Compute Plane:** Stateless PHP container pods run on Amazon EKS (Kubernetes) across multiple availability zones.  
* **Database:** PostgreSQL runs on Amazon RDS in a Multi-AZ cluster, with read-replicas for load balancing.  
* **Secrets Storage:** Dynamic secrets and credentials are encrypted and injected into containers at runtime using AWS Secrets Manager, keeping configuration values secure.

### **5.5 Monitoring & Incident Management**

* **Platform Dashboard:** Prometheus and Grafana track server resource usage, request latency, and application health.  
* **Logging:** Logs are aggregated and indexed to support search and diagnostics.  
* **Alerting Rules:** Critical exceptions or errors trigger notifications to Slack channels, while critical infrastructure failures route to on-call engineers via automated paging systems.

## **6\. Security, Controls & Compliance**

### **6.1 Access Controls**

Security in RAAX is enforced through a strict defense-in-depth model, starting at the API gateway and extending down to database row-level permissions6.

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

3

* **Decision:** Implement multi-tenant data separation directly in the database engine using PostgreSQL Row-Level Security (RLS) instead of manual database queries3.  
* **Rationale:** Manual application queries (such as adding WHERE tenant\_id \= x filters) are vulnerable to coding errors, which can result in data leakage4. Enabling RLS on PostgreSQL tables ensures that the database engine enforces isolation for every query, regardless of the application-level implementation4.

Database connections use a dedicated non-superuser role (app\_user) configured with the NOBYPASSRLS attribute5. When a request is initialized, RAAX resolves the tenant context and sets the session value on the connection3:

SQL  
\-- Enforce security validation using isolated credentials  
CREATE ROLE app\_user WITH LOGIN PASSWORD 'strong\_password' NOBYPASSRLS NOSUPERUSER; \[cite: 5, 20\]

\-- Session initialization function  
CREATE OR REPLACE FUNCTION set\_tenant\_context(tenant\_uuid UUID) RETURNS VOID AS $$  
BEGIN  
    PERFORM set\_config('app.current\_tenant\_id', tenant\_uuid::TEXT, FALSE);  
END;  
$$ LANGUAGE plpgsql; \[cite: 3, 4\]

\-- Apply the RLS policy to the target table  
ALTER TABLE accounting\_journals ENABLE ROW LEVEL SECURITY; \[cite: 5\]  
ALTER TABLE accounting\_journals FORCE ROW LEVEL SECURITY; \[cite: 5\]

CREATE POLICY journal\_tenant\_isolation ON accounting\_journals  
    FOR ALL  
    TO app\_user  
    USING (tenant\_id \= NULLIF(current\_setting('app.current\_tenant\_id', TRUE), '')::UUID); \[cite: 3, 5\]

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

* **Asynchronous Event Subsystem:** System processes dispatch clear domain events (such as PurchaseOrderAuthorized or SalesInvoiceFinalized)7. Custom plugins or integrations subscribe to these events through background queues, ensuring that failure in custom code does not impact core execution7.  
* **Static Interface Contracts:** Custom adapters must implement strict PHP interface definitions, registered with Laravel's service container7:  
  PHP  
  namespace App\\Contracts\\Procurement;

  interface SupplierValidationInterface {  
      public function validateSupplierTIN(string $tinValue): bool;  
  }

### **7.3 Upgrade Strategy**

The upgrade pipeline ensures that core system packages can be updated continuously while maintaining backward compatibility.

* **Database Schema Evolution:** Database changes are designed around the "expand-and-contract" pattern to prevent application downtime during deployments18:  
  * *Expand Phase:* Add new columns as nullable and deploy updated code that dual-writes to both the old and new fields18.  
  * *Transition Phase:* Run background migration processes to backfill existing records.  
  * *Contract Phase:* Remove the obsolete columns from the database once all services are updated18.  
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
| \[ RAAX \]  Global Search Dashboard (Ctrl \+ K)     \[Profile\]   |  
\+--------------------------------------------------------------+  
| \[Sidebar\] |  \[Tab 1: Journal Entries \]  \[Tab 2: Stock Levels\]|  
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

* **Phase 0: Discovery and Architecture (Months 1–2):** Define backend structures, map data schemas, and test database RLS policies3. Deliver base environment configurations and CI/CD pipelines.  
* **Phase 1: Foundation & Core Modules (Months 3–8):** Deploy identity registries (SSO/RBAC), basic HR directories, and the core double-entry finance ledger (journals, ledger accounts, and AP/AR aging matrices).  
* **Phase 2: Supply Chain & Sales Integration (Months 9–14):** Integrate procurement workflows, multi-warehouse inventory, customer orders, and localized compliance tax calculation engines1.  
* **Phase 3: MRP & Multi-Branch Scaling (Months 15–20):** Deploy advanced production tracking (BOM)2, branch stock transfers2, and asset registers. Finalize legacy system migrations and decommission legacy tools.

### **9.2 Team Structure**

Building RAAX requires a cross-functional development and operations team of 11 dedicated professionals:

* **Principal Enterprise Architect & PM (1):** Oversees database schemas, system architecture designs, and release milestones.  
* **DevOps / Infrastructure Specialist (1):** Configures AWS EKS infrastructure, manages Terraform deployment assets, and monitors CI/CD pipelines.  
* **Backend Engineers (4):** Implement core business logic, write APIs, and build database models and test suites.  
* **Frontend Specialists (3):** Build responsive data views, design layout components, and optimize UI performance and accessibility.  
* **Quality Assurance Engineers (2):** Write automated integration tests and perform end-to-end security validations.

### **9.3 Project Timeline and Dependencies**

* *Timeline Overview:* Development is scheduled over a 20-month timeline, leading to a complete production launch at the end of Month 20\.  
* *Critical Path Dependencies:* Setting up the core identity layer and database-level RLS policies3 is a critical path dependency that must be finalized in Phase 0 before beginning Phase 1 functional modules. Similarly, completing the Finance Ledger domain in Phase 1 is a hard dependency for integrating the purchasing and inventory engines in Phase 2\.

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
| **System Uptime** | Maintain ![][image22] core system availability | Monthly review | AWS Route 53 health reports |
| **Database Latency** | **![][image23]** query latencies | Continuous tracking | Prometheus monitoring dashboards |
| **Queue Completion** | **![][image24]** background task execution rates | Daily monitoring | Laravel Horizon administration console |
| **Data Processing** | Zero manual ledger reconciliation errors | Monthly review | Accounting reconciliation tests |
| **Audit Readiness** | **![][image25]** reduction in annual audit compilation times1 | Yearly review | Audit cycle duration trackers |

### **11.2 Governance Model**

* **ERP Steering Committee:** Features representatives from executive leadership, finance, HR, and operations. The committee meets monthly to prioritize features, review project milestones, and coordinate cross-department integrations.  
* **Change Control Board:** Includes product leads, security experts, and DevOps managers. The board reviews and authorizes production releases, manages environment configurations, and coordinates disaster recovery testing.  
* **Operational Review Cadence:** Technical teams hold weekly stand-ups to track progress, while executive steering groups run monthly reviews to evaluate KPIs, budget alignment, and long-term release schedules.

## **Executive Summary**

The RAAX Enterprise Resource Planning (ERP) platform is a high-performance system designed for organizations with over 1,200 employees, built to scale cleanly to support multi-entity holding structures. Operating on an API-first framework, the system provides full control, detailed change tracking, and strict security, while explicitly avoiding complex, client-side artificial intelligence features in favor of predictable, audit-ready operational workflows.  
By implementing a Modular Monolith architecture using PHP 8.3 with the Laravel framework and PostgreSQL 167, RAAX balances database performance with design simplicity. This architecture groups code by business domain, keeping the system easy to maintain and test, while allowing modules to be split into standalone services in the future if operational demands change7.

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

Data security is managed directly inside the database engine using PostgreSQL Row-Level Security (RLS) policies3. RLS ensures that database queries can access rows matching only the active user context4, preventing data leakage across branch operations at the query level4.  
The deployment roadmap is structured into clear, manageable phases. Initial phases establish a secure foundation, integrating core finance and human resources modules. Subsequent releases expand capabilities to include procurement tracking, warehouse inventory management, and localized compliance tax reporting. Supported by a DevOps-minded engineering team, automated test suites, and robust cloud configurations on AWS, RAAX provides a stable digital core designed to scale alongside the enterprise for years to come.

#### **Works cited**

1. VAT compliant ERP software in Bangladesh | mysoftheaven.com, [https://mysoftheaven.com/pages/vat-compliant-erp-software-in-bangladesh](https://mysoftheaven.com/pages/vat-compliant-erp-software-in-bangladesh)  
2. NBR-Compliant VAT Software in Bangladesh – GPAC ERP, [https://gpacsoftware.com/vat-software-bangladesh/](https://gpacsoftware.com/vat-software-bangladesh/)  
3. PostgreSQL RLS \- Tenancy for Laravel v4, [https://v4.tenancyforlaravel.com/postgres-rls/](https://v4.tenancyforlaravel.com/postgres-rls/)  
4. Row-Level Security (RLS) in PostgreSQL for Multi-Tenant SaaS Apps | by Anand Thakkar, [https://medium.com/@anand\_thakkar/row-level-security-rls-in-postgresql-for-multi-tenant-saas-apps-ef8c324031d0](https://medium.com/@anand_thakkar/row-level-security-rls-in-postgresql-for-multi-tenant-saas-apps-ef8c324031d0)  
5. Multi-tenant data isolation with PostgreSQL Row Level Security | AWS Database Blog, [https://aws.amazon.com/blogs/database/multi-tenant-data-isolation-with-postgresql-row-level-security/](https://aws.amazon.com/blogs/database/multi-tenant-data-isolation-with-postgresql-row-level-security/)  
6. Multi-Tenant Laravel: Architecture and Tenant Isolation \- Intelligent Graphic & Code, [https://www.intelligentgraphicandcode.com/development/multi-tenant-laravel](https://www.intelligentgraphicandcode.com/development/multi-tenant-laravel)  
7. Laravel Modular Monolith: The Powerful Architecture You Need \- iFlair, [https://www.iflair.com/laravel-modular-monolith-the-powerful-architecture-you-need/](https://www.iflair.com/laravel-modular-monolith-the-powerful-architecture-you-need/)  
8. Modular Monolithic Architecture in Laravel 12 \- 200OK Solutions, [https://www.200oksolutions.com/blog/modular-monolithic-architecture-in-laravel-12/](https://www.200oksolutions.com/blog/modular-monolithic-architecture-in-laravel-12/)  
9. Bangladesh VAT Compliance | Frappe Cloud Marketplace, [https://cloud.frappe.io/marketplace/apps/vat\_compliance](https://cloud.frappe.io/marketplace/apps/vat_compliance)  
10. Top 10 VAT Management Software in Bangladesh (2026 Guide) \- Pridesys, [https://pridesys.com/top-vat-management-software-in-bangladesh/](https://pridesys.com/top-vat-management-software-in-bangladesh/)  
11. best architectural patterns approach for senior Laravel developers \- DEV Community, [https://dev.to/abdulsalamamtech/best-architectural-patterns-approach-for-senior-laravel-developers-8m4](https://dev.to/abdulsalamamtech/best-architectural-patterns-approach-for-senior-laravel-developers-8m4)  
12. Building modular systems in Laravel — A practical guide \- Sevalla, [https://sevalla.com/blog/building-modular-systems-laravel/](https://sevalla.com/blog/building-modular-systems-laravel/)  
13. Exploring Modular Monolithic Architecture: A Laravel Developer's Guide with an E-Commerce Example \- Medium, [https://medium.com/@harryespant/exploring-modular-monolithic-architecture-a-laravel-developers-guide-with-an-e-commerce-example-0548668ec222](https://medium.com/@harryespant/exploring-modular-monolithic-architecture-a-laravel-developers-guide-with-an-e-commerce-example-0548668ec222)  
14. Modular Monolith in Practice: Problems, Patterns and Practical Examples (part 1), [https://phpconference.nl/session/modular-monolith-in-practice-problems-patterns-and-practical-examples/](https://phpconference.nl/session/modular-monolith-in-practice-problems-patterns-and-practical-examples/)  
15. API Rate Limiting from Scratch: Token Bucket, Sliding Window, and Distributed Strategies | by Arslan Ahmad | Stackademic, [https://blog.stackademic.com/api-rate-limiting-from-scratch-token-bucket-sliding-window-and-distributed-strategies-4c3d58924513](https://blog.stackademic.com/api-rate-limiting-from-scratch-token-bucket-sliding-window-and-distributed-strategies-4c3d58924513)  
16. How to Design a Scalable Rate Limiting Algorithm with Kong API, [https://konghq.com/blog/engineering/how-to-design-a-scalable-rate-limiting-algorithm](https://konghq.com/blog/engineering/how-to-design-a-scalable-rate-limiting-algorithm)  
17. Rate limiting with Redis \- Ramp Builders Blog, [https://builders.ramp.com/post/rate-limiting-with-redis](https://builders.ramp.com/post/rate-limiting-with-redis)  
18. Database Migration Strategies: Safe Schema Changes \- Intelligent Graphic & Code, [https://www.intelligentgraphicandcode.com/development/database-migrations](https://www.intelligentgraphicandcode.com/development/database-migrations)  
19. Building Multi-Tenant SaaS with Row-Level Security in Laravel \- DEV Community, [https://dev.to/addwebsolutionpvtltd/building-multi-tenant-saas-with-row-level-security-in-laravel-3kd3](https://dev.to/addwebsolutionpvtltd/building-multi-tenant-saas-with-row-level-security-in-laravel-3kd3)  
20. codingways/rls\_multi\_tenant: PostgreSQL Row Level Security (RLS) multi-tenancy for Rails. Automatic tenant isolation, context switching, security validation, and Rails generators. Perfect for SaaS apps requiring database-level tenant separation. \- GitHub, [https://github.com/codingways/rls\_multi\_tenant](https://github.com/codingways/rls_multi_tenant)

[image1]: <data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAALEAAAAWCAYAAACPMH2TAAADv0lEQVR4Xu2aS6hNURjH/0IReT8S5YhIniFFBgaIgVIUEyYGJGWgyOxOFEmJAckjSgZKykQYnJBEkfIoMSBShCgDb9//fnvZa6+z99ln3eM+zu371b/TWY999r3nv77vO2ttwDAMw+gZDBONa0JjYBjdzEHRH1FVdLyO7oheiT4m452+wTC6maFQMz4TTQz6ipgjOovUyHksg5re1yVkF8Zm0Ug3IYKbSBdUNduVyypo1jB6MZ+gZrwONXWjzBY9FY0NO4TxonWirdBr30jeO+0XfRZ9F/VP5jQKTXkKaQapxwDouIeoXTDsYzll9AL6iY5Cv+xfQV8jnINeI4/B0OvuDjsSXP8uUZ+grx7zRV9RbmJSFIm3iM6EjUbrwgjMSExDVbJdpTDC9Q0bE8pMTH6L3otmhB11iDFxHgNFV2Am7nXQRDRTbFlRj0ZM/Bo6pi1oJ0z3LFfCkiM0cdE4wrZRSBcaIz4jPz+zyMS87+miCdAdmBHZbqMnwy/YlRVrg76O0IiJq9AxL6BG5A9HfzHRhKeh97Rcp/wzMef5WWAF0nn8W5glOO4l0pKCn7EJOvdC0s5xHE8xQjNSO+6KVnvvQ7hA1iBb85dpVvtMo9NwZUVsis8jxsTOaJehJcZKb8wS0Q/R+eS9MzF3KnzcIuQPSgev65uY0JR5kZj3ezV5dZyAmbjlqECjIr9kGroZYkx8TzRc9EX0QbQI6aHKPNHbZAwJywkfXquK1IgxJuYPVLY/F+0VLc30Gi0FSwmmb26/NUMjJn4MHcOSwUVSHqJw1yM8dNmXzCkzsW/aGBOTR9A+pwPIlhdGC8B68hrit73yKDOxM62fwhmFaVAatYgyE1cRF4n5yrZBoqluUAKzEhcay4Y8eLDD8sc3fpmOtM80Oo1joj0o3jaLoZ6JaeD1qD0tZA3KOdu9NjITeupH/ndN7EzM+93gBnl9rtQxWgDuBNDEeVtVHYEndzRLG9JFwe0w/+i6krQ7aGimdJYyi5M2Rkia2+2YOBOz5PEXG/tZdvj3/070RjTZa+P2GSP+fWjmuSiaBjUxF9WUdGh7ljiE5rOS0QXwS2IJEbtHzO2pMGq7SFckmu9WMi6PIaLDop/Q0oDPSmxEaiSamGabCzX8SWi05DG2q19pyCqyn+siL6+zDTr+gWhn0sY53AGh6Rn1OZ61eez/xOgGFkJNxSgYw2jUpl/D6HIWiJ5AjRxDBRr9JgXthtHl8AkvV2s2AutJpnqmYqboood/DKNL2IHa535jdBuGYRhGx/gLfW4ZRRcknsMAAAAASUVORK5CYII=>

[image2]: <data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAmwAAABICAYAAABLN6ksAAAErUlEQVR4Xu3dW6htUxgH8CGXyCUhl6hzSG7hRcglnSS5RHIpl/Iilzfx4FLyIkkeSKJQ4kXyQEkkdQ4pD569iDrkEpISSq7fv7nm2XPPM9c6+5y9zyX9fvVvrTnHWHuuuZ6+xhhz7NYAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA4H/jiMo1lfsr/1Qerlw/y22VjZVPKsf1H9gF9q1cVblh3DDH/pWbKi+PG8qBlacrv1Req/xUeXxZj+k+ByzrAQCwB7i8srly7Oj8XpV/K69W9hm17Qwpnr6tvNRWViT+Wvm78nubLtieb93fO2V2fE7l57b8Xqb6PNd2zf0CAKzYo5W3WzdaNZaC7cvKMeOGNXR45YnK97P3K3VkZe/WjRBOFWx/tO7ehjKadt7geKpPzg37AADsVidUvqtcOG5o3YhbCrZnWjfatpbOr3xc+bR106CrMVWwHdS6757p1aEcv9+60bx5fXKu7zNPppP7753C8dBBWxzVpu8r1zy1dSOI+dxhy5sBALZ2desKlBQYQynQ7qt8Vlk3aluNFDE3Vt6rnNvWphCcKtgyIjhVjOU4RWIKrnl9cq7vM5Zrpf23ygWVxypft25qNoXYSa1b+/dD69YFDtfMHV35oHW/a0b1/mpbXxsAYJmM9mxqXQHy1SApPj6qXLml59pIkZLiZrzwf7W2t2Drp3jn9VnJNPB45DEjZinQLtvSY6m4i/63zmvvxbb1tQEAlsmIUJ6KTKGxyGmtW/uV1976ys2tK2pOr9w6aFsk04x3V16pnDxq21G7q2C7dnCcvuPP3NKWCrY8xJCHNz5vXeG6YXYeAGChvqDYPDo/dF3lndattcqo2yWz82e1blown/+xcvHs/EplO5FMO2Yd22qnRXdXwTb83FTBlva+YIt1s+M+edDCFiIAwELZYy2Fw53jhoFhYZIRpYzIRQq2u2bvV+v41m3lke01Fi30n2eqYIupe7u3daOFvak+OTfsM2V7C7bcV7YLGfqidUUrAMBcGSHLHmZnjxsGhoVJRuT+nL1PwfZg5Y3KHW1tRoqypUfWuD1UOWTUtsi8gi1TveMtO1KIDdeZTfUZr0Wbsr0FW9aubRy0Rb5zimYAgEnZcy3FxDdt8Vqy7I3Wj0ClsOkLkBRsb7VuOvOR1j31uRYyEpX/snDPuGGO3MeTldfb8gX9ke+UUaz1s+O85nhYXE71yX0tKkDzHfM7pIDNPnBxZlv+W+Z8Ctn0y5Ox/UMHJ87aI9d+anAMALBDUpBdVHm2cntbmhI9Y5bo17PtiVI45ftlK5FsuZH7GRv32Rly3YNbd63sv7ZofRwAwHZJUdaPCmWE7d22NFqUPdpiTy7YAAD+9zZV3qzsV/mwdRvtZouKFypXzPpkajBPim5Lpi2H+70tygOzzwAAsA2Zwst+bZeOG1q3NmtDm/4fpAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAsAf7D3Yq3wPJOlGVAAAAAElFTkSuQmCC>

[image3]: <data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAmwAAABICAYAAABLN6ksAAAFA0lEQVR4Xu3dWahvUxwH8CVDZEjIEHUvycyLkLGbJEMkQ4nyIsObeDAlKUnyIolCiZLkgZJI6iLlwbOUqEuGkJSizNa3tfc5+7/v/p9zr/O/Q/p86tf/7L3WPv+9z9O3tdZepxQAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA+N84qNYVte6q9Xet+2td3dWNtTbW+qjWEf0F28HutS6rdc24YcIjtb6t9XKt32t9Uuu0QfvetR6v9XPX58fSrhma6rPXTA8AgJ3AxbU21Tp8dH6XWv/UeqnWbqO2bSHh6Ztaz5XVQ2Lu7e1a+3XH59b6rdZPSz1Kebq033dcd3x6ae3DZ5nq81TZPs8LALDFHqr1Rq09xw2lBbYvah02bligA2s9Wuu77uctcUiZDZO5v9xnzvUS4PJsQxlNO3NwPNUn54Z9AAB2qKNKm1Y8Z9xQ2ohbAtATpY1oLdJZtT6s9XFp06D/xXllefry1Fq/lDa1G/uUdu+ZXh3K8TuljebN65NzfZ95Mp3c3/fBtfYftEUC5dRz5TuPL20EMdcdMNsMALC5y0sLKAkYQwlod9b6tNa6UdtaJMRcW9p05hllcUHwsdKeI1OckRG3qTCW44TEBK55fXKu7zOW9X5pTzg8u9bDtb6q9VdpQeyY0tb+fV9aeByumTu01nul/V0zqvdn2fy7AQBmZLTn3dICyJeDSvj4oNalSz0XIyEl4Wa88H8tEvryksSm0qZ1E5hiXhjLcT/FO69Pzq02DTweecyIWQLaRUs9lsNd9H/rfPaeLZt/NwDAjIwI5a3IfhpxnhNKW/uVz976WteVFmpOqnXDoG0lmWa8rdYLtY4dta3F0aW9PJCRr5gXxhYZ2K4cHKfv+Jrry3Jgyzq7rLf7rLTguqE7DwCwoj5QbBqdH7qq1pulrbXKqNsF3fl+zViu/6HW+d35LZXtRDLtmHVsi5gWze94sbT7ydqweWFskYFteN1UYEt7H9hiXXfcV160sIUIALCi7LGW4HDLuGFgGEwyopQRuUhgu7X7ea2OLG0rj4yQrbTQf+iB0qZuNwzO3Vva/fahcurZ7ihttLA31Sfnhn2mbG1gy3Nlu5Chz0sLrQAAc2WE7Ncyu9ns2DCYZETuj+7nBLZ7ar1a6+aymJGibOmRNW73leX91eZJoMpUbh/OhiNsCYCR9vGWHbluuM5sqs94LdqUrQ1sWbu2cdAWz5cWmgEAJmXPtYSJr8vKa8myN1o/ApVg0weQBLbXSwtKD5b21uciZCQq/2Xh9nHDSDbKzVq4fjr1xNKmZvv7i9xTRrHWd8f5zPEwXE71yXOtFEBzj/meBNhdu3OnlNm/Zc4nyKZf3oztXzrIWrtevjtvtwIArEkCUfY7e7LWTWV5SvTkrqJfz7a9JQjl3rJNSD6n9j1LcMr9pU/eIO0D3tC4z7aQ7923tO/q19gBACxEQlk/KpQRtrfK8mhR9miLHRXYAAAoLZi9VmuPWu+XttFutqh4ptYlXZ9MDWY6cjWvlNn93laqu7trAABYRabwsl/bheOG0qYgN5Tp/0EKAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA7sX8BdVrwY4DQ/2UAAAAASUVORK5CYII=>

[image4]: <data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAmwAAABICAYAAABLN6ksAAAFLElEQVR4Xu3dX8huUx4H8CVGZhh/ZiZGhnNIY/7gRkOGdJLkf/KnJkpqGlyoiQtMTW4kaW4kzRTqxI3kAkkkOUgprlxIydQhRkNMTVFj/Pt9rWefdz/7PO97znvOe/6kz6e+vc/eaz3vs/dz9WuttdfTGgAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAB8b/yscmnl1srXldsrV8zyx8qmyuuVXwxv2A1+ULm4cuW0YYGLKv+uPFl5p/JW5Xej9gMr91b+W3m08knl7lF7LOrzw7keAAB7gfMrmytHTc7vU/mm8khlv0nbrpDi6V+VjW37isQ3KsfPXu9b+Uvl06Xmdn/r/+9Xs+NTK/9p8/eyqM8/2u65XwCA7XZn5enKAdOG1gu2dytHThvW0E8rf2t9tCyvt8dBlRsn5w5rfURw8L/W720so2mnj44X9cm5cR8AgD3quMqHlTOnDa2PuKVgu6/10ba19PvKq5U3W58GXa0UZ7m2TOMOU5gZbds8e52CLu2ZXh3L8fOtj+Yt1yfnhj7LyXTycN2HVw4dtcURbfF95TN/3foIYt73k/lmAICtXdJ6gZICYywF2i2VtyvrJm07I0XMHyrPVU5rO1cI5rqTrF87uvJS69OZkRHBRcVYjlMkpuBark/ODX2mst4v7Z9VzqjcVXm/8lXrhdgvW1/791Hr6wLHa+Z+3vo15nvNqN6XbevPBgCYk9GeF1svQN4bJcXHK5ULt/RcGylSUtxMF/7vqGNaL45SGOUevqhcM2tbrhjL8TDFu1yfnNvWNHD6jEceM2KW6zhvS4+l4i6G7zp/Bw+2rT8bAGBORoTyVGQKjZX8pvW1X/k7WF+5qvWi5sS2VChtS6YZ/1x5uHLCpG21cu3XVn7UerE2jLjFcsXYWhZsl42O03f6nqvb0vXkIYY8vJHRwBSuG2bnAQBWNBQUmyfnxy6vPNP6WquMup0zO39K69OCef/HlbNn57dXthPJtGPWse3ItOhvW38qdHhvisnXWr+eY9vyxdhaFmzj9y0q2NI+FGyxbnY8JA9a2EIEAFhRnqhM4XD9tGFkXJhkRCmjWpGC7YbZ652VAmtj69trrLTQf+yhyo+nJ1u/1qGoXHRvN7c+WjhY1Cfnxn0WWW3Blvsa1tcN/tl60QoAsKyMkH3e5jebnRoXJhmR+//sdQq2jHA9Xrmurc1IUbb0yBq3v1YOnrRNZX1YpmKnco0pACNTvdMtO1KIjdeZLeozXYu2yGoLtqxd2zRqixSd421IAADmZM+1FBMftJXXkmVvtGEEKoXNUICkYHuq9SnJO1p/6nMtZCQqv7Jw07RhYl3lhTa/LUa29civHQzTpLmmjGKtnx3nb47HxeWiPrmvlQrQXGO+hxSH2bA3Tm7z32XOp5BNvzwZOzx0MGz0G/nse0bHAAA7JMXPWZW/V/7UlqZET5olhvVsu1uKojw4kW1Czq0cMt/8nfTJ9aVPttwYirmxaZ9dIZ+bKdx8VvZfW2l9HADAqqQoG0aFMsL2bFsaLcoebbGnCjYAAFovzJ6o7F95ufWNdrNFxQOVC2Z9MjWYJ0W35bE2v9/bSrlt9h4AALZhmHbMlONU1mZtaIt/gxQAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAPZi3wIESe8Z568PgwAAAABJRU5ErkJggg==>

[image5]: <data:image/png;base64,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>

[image6]: <data:image/png;base64,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RERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERGRX/hfsZYMvHVjt3gAAAABJRU5ErkJggg==>

[image7]: <data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAD4AAAAZCAYAAABpaJ3KAAACnUlEQVR4Xu2XzcsOURjGL6GIEHp9RO/GR0gpNjYWklhYoYSFnYU9/gELkYUFJdFrRxbko0h6YiHsREkUskHsWPi+ru455n7OM2femeFlc3511TznPjNzX2fOuc95gEwm8x+YQ82jJrm2KdRE93vMGEctihtrmAVLzjOemh+1pdD71lH3qO/UG+ozdRb27JvU6qLvEHWXeu10iTrltB2D+dSiBJZTl6mX/aFaDsASvg978S3qC3XSd0qgBGXwJ3UbloPQwO2kblCfUBqfTG2mdlAPYfdti6Rc3lPri3tq0VSaCnuBXvSqP1yLjF+nTlB7qYX94SQyqcTvUNOjWOA0+o17RmD3xyyAzRrFjkexJF2NS21ZAUtuVxxwbKA+op1x1Qjlr5j6NOJfGtc9H6hlccChr/cI7YwHD4ppSTTiT4wvha3x3Ri9uEygLlJPqNlRzKM1vQ/2FWNSxlUr1H4ULXaDrsZlOLxkMfWOevC7xyCqJ71Cuu5CMK5B8XoOy38rymI5Kl2MxwRTVV8joBmh6t/DoHENYNjPvWb4Tkh/8WHqGSy2P4ol+RvGwzSuSspzCFZ9tY49K2HrWnu0niGpwB3znZA2LjQLFXsbB1K0Nb4G1vcc+r9cXVKBtdQPalMccOgg03Y7E1tQDloj2hoPI6sDw5KirclUF5rS6nOhuK5CuXQxri1Ssa9xoAoVAo2+Oqs4xYXBj6KuRThCbix+6549RZ8mJzdtZS9gp62DUWwm9Q3VxlUjrsDe42eaBvBa0a6lssrFKlHxCKa8eigfPEw9pp6i/LpCD1fbVVh/mTgC24qaMA12wtJ94dx9HrbHz6XOoDTuDycpaV0fhj13zNFI64+GZoIqchf0j0wHDk1TzaDUMTaTyWQymYzxCwwPtBQZIPL1AAAAAElFTkSuQmCC>

[image8]: <data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADYAAAAZCAYAAAB6v90+AAADDUlEQVR4Xu2WTahNURTHl1Dk+6MnUd5AykgIJR8TxIDCwEAxNDFTlKQ7MWRADKRkIF9hQimKKIVixIDkEskAUaQU1u/uvd9Ze91zzn03kzc4v/p3ztl7n3X23utjH5GGhv9lnGp2vFYxWjXJN45ktqneqy6pnrq+xCjVftVJ1RjXV8lU1RLVHN9hwPCAhHHsXD9Y+2NdH3xSbYz3K1SHVTOK7g6nVM9Vc117JZdVX1XnVM9Ua/PuDoOqh6q3EsY9Vi2wA2qYLrn9V3l3B+wShoA3jqseqT6o3sXrN9WWOKYnLdVB1/ZZdUEKd3P/V0K4JNap/kh4v46WhHctCyW3P1F1L14TB8w9LJY+PDVN9US107Wze20pwvKj6rdqVRqgLFX9kO4JWZJ9vzA805bC/gTVA8mLgp0T9m+Y557wARax2bXThjfwCrAAxGISaWF4Fw+UkeyXLczaBwpH8sh4yb+1T0LBGDZ1C7OhV7ewn6plpt1StzAf2m0JFY+itEOKAkMI4s1hh2GCWPY5xm7y4RTnZ+Kzncje2Ib8xliw4RdG9bP2yygr7eclFCGOg6ooGWKK6qVqnnmm9PLhPbGNPsbcif1wS4LHfEh5GM971v5Fye2XsVxyTxGepyV4cqXqbmyvhQpFSFFWOSdeS/jwJjNmUHVf9V3COJKbe7SoGFYK5d7a511v33NT8tKOl1PxwpsnTF8t/MZwIPISeVFVFKhQHLYpx5jozGxEOdY+OVZlHxhjQxCOSJ7jdeHfARfPd23srj1nJkvY3XSAAvnGrrdMWxnYX+/aKDbWvocw9MWCw90uzB49pTDBqxImABgladm1BMWFRRw17Xhr+9CIAB9nHB5PpA2w9r9Ibj+RCsYx3yEhH231rSs8HUhmkpTEvCLBW/5fjsR/IeGQZBzXXdI9ORbxS3XWtGH/tuT2D5l+C4umKAz4DgkepBID86Eg9WSNhITeKt0/ngn+DjaodsdrP7BRveyTu9dVq32H4Y3qmoQK7aNlxIL3+aXyUWBhg2ZJ9S9cQ0NDQ0MX/wAMwabyuTb9ZgAAAABJRU5ErkJggg==>

[image9]: <data:image/png;base64,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>

[image10]: <data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAE4AAAAZCAYAAACfIRhSAAACRUlEQVR4Xu2YzytlYRjHH0kRJs1MpJSsJiWbaeY2sWE/sSSRWMusSM1/MIspsZRYYaFkZ3UXExtLNqKQFLKwoKSY77fnvPXe957765xzy+X91CfnvM977j33e94fJyIej8fjictH2Ow2hnAOL+AN/OrUKpov8BdscAs5qIeT8A7OOrUwxuAJvJcKD64a9sN9mIJVmeW8NIlevwJfpLjgCPtXdHAD8BDuwG6nVgrvJrg6OAGXYYdTi0Kc4Gpgi+RfGlhrFe0XBkf9Z9HPsuGMcD+XfSn78hoeF4Tr0TS8gotOLQ5Rg+sR3Swu4TNcszsFbMIjuAS34Lhk/lh+L7VHMI9Nu7kn/jVtU/BUdIM6COo5YWi8SQbH4ySJEhz7L1htP4M2A0fEBuyy2ghnySP8EZxzRKUle+qboOx7Mn1HRNfyQThn1XPyCf4RHXG/nVocogbHsAxucJ3wVnQ62ZhA5oPzKMF9s9pK4gOcEQ2RYcalHMGZc3eNMoGkRWtRgou9KXGD4PTl+hFnkyhncI1WG3kVwRGuJUOiL6V8+Y1COYLjj2MY3E1tkpiqiQRnwwUzJbrjcisvBm4026I3+dephcEHtS7anw+M8Ht5zLbaoI20i+609mb2ILrM8BoDQ3yCvcH5d3gt+nmrppPosrQH+6S0F/3EMU/V1R5JLmF9z5w2e5RwZ2VY/+AxHJbsd682uCsaFkczHZXM7wi713z3+SYwL8CF/onAWWKmNkcuj5PY/DxR4JzmEyjGYtc3j8fj8XjeF/8BfKqqbT4tod8AAAAASUVORK5CYII=>

[image11]: <data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAHYAAAAZCAYAAADkBdqeAAAD9klEQVR4Xu2YXahUVRTHl6iQqH0QepUSE00RSYMkMXwoLShQiSSUfPDBB8WC0KAgCMIXCfHFt0IRg8iM8CUhImRAUFApECsQfSjUh0CE0CilbP1YZzFr9j3nzNw7M7cZ2j/4w8zea3+uvdfe+4hkMplMJpMZZqapJqWJyoOq6WmiMkusTL+g3VfSxP8zU1UbVK+nGRVgv071g2pGkgfvqe6rrqiOqr5QXVddVi0Mdr3kAdUJsXYfTfL6Ae09nCYOAgx+v+qj4nenjEhzITCJVY49rzqo+kS1WMp3dq+ZyB27Q2zRDgxLVJ+qbqi2J3ljoZ1jB2rQPYbj5BsZkDFOVj2n+lH1qtiu64Z+OZadTZjz30STdLfPlaZNCuNirBHqaFeng12aR8iN43xXbOx1Y6QfRDf6WhayH1c9KdaXKTK6zbbQwBax8+1bGUcFFXTiWAb1QaF5LRaj8foQC+8t1VXVr2Ln81rVHNXnYtHmH9Ux1UNifWgUZRHtQlWdd8XO/6cLO+x/CbY+Jr8rIH7DUyEtKs4Dc/6H6mvVl6o/xfoOzP9m1ceqN8XuBb9L+TxWQsi4JmM/RzuhzrGvqb4Tm3RgoAzgb9VGN6qACSTMxdvzabG2cIzj7dOO4w5yxzrYxTpnitXJhK5wI+UZ1R0ZPaboWPC2y3bsS2LjjP1nR/6lWi1WlkUZoQ9pm23h2fG22CrnbO0VdY4tw1c/E1wHdu8naQ2xCWfinTWqe0WeU+fYWKfv8LTOXjj2kFgefYgibauYc3HyV6ptYq+E9OgYE+wawtFZ1SrpPiSP1bHsYuyJIHUwgXESoSHVTiDPqXNsrLNfjo1HAq+BVNzYmfd3ChsXx0LX4FTOWr9EjZc6x1I3ITKGf7dn4uoYVsdy6SI6Mm7y2sFzCdvbYvaLWrPHzwLVEbEnT9lXonbUOZZ0zpnnQ9qeIp3LSx3D6ljSgLd7nWNfVO0N/4mkH4o5uufgWBy8O82ogM7sFBsA1/aUk2I3Pw/3z6puid3M57tRCdR7QGzg8TnGFy5umS+I1YleFjtjOVqc5arfpPXrFvXQz1jnY9JapzOi+ln1SEjbJFae+4lfBpeqbqq+F4tKpPN9ADzUctbGC9RxMTtfFPFcXa9aFv7/J9CpMjWkudJx9jmxmydnC8+LM6onivwyfMBRb0jrMwaxI9lVMe2zEjt2U1WdaVrc4ZyDl8T6TajcF+wQdeK8XWLjwpZF4osY+M0CIf+C6qI0nzuUp8xPqsOqU2JPuqGB3bFS7CbICu/q5jfBzBZzti9UfrMzo/OAczXaRbClDPnxAwVlEGXIo61hmptMP2HV+DuqE5WtvEwmk8lkMplMZjj5F4h7GbT16GYzAAAAAElFTkSuQmCC>

[image12]: <data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACYAAAAZCAYAAABdEVzWAAACmklEQVR4Xu2WzctNURTGH6HIt0REPsrARyRRykDiLQNSlPIx8QeYKGJuQBEiJQaUfEUGvBkYnJgoSpSUKCSSUIqUxPO8a6979lnn3OtO3qLuU7+6Z+999l5r7bXWuUBP/47Gk6FxMGhEHBhMDSenyGNyhUysTrc0jtyKg+0kL1eSuXEiaQoZEsZ0wNjseTd5Clu7j7wgG2EGS3p/EXkEc+CvknfPyVlSkB2oG/GafCCXyWnyinwmS9L8JJhR59LzHNheJ2AGviHvyRPyjExP69pqBZkaxg6QO2RUNvYAdoiMOoQyCq6l5BvZm55Hkxtp3KWIniTDsrG2Wk8WhjFtXsA2d+k5OpBrMflK9qfnMbCbUOQkRfYeuoiUax0stLp7121yFNXrLNDZMOXbfXIB9p6cvYbSuZtkQ/rdlSaT34njsErSAdGIAub9Vth1zkc9DzX3kSyHrdmezXV9hbl2ku8oDdQmeX5JD2EHug7C1uaHNym2hmWwdvIF9TMqGknukmlkNayCdKA8jgmeS7mpdYquDm+SIroH5qi0AFadm2AN+Eha0yhVyqzsWV7owF9kVTYepZ73k3wi88KcSxH2hJcBqmpV94Q0r1yenX5X5L0nSl7KOC99Re4dWdtaUbYHkbcEl1+hJ7yMkVEFyoI4Q9ak3xUpwdU4o+ThW5SGaZ0MPdxaUV6lmqa3hFx+hZ7wflaB0jD1S+3TKCXvFlTvWp6oB+U5dhH1hqsE9s6fS9HKoyvpo60I6mrV46SraJ8GA0n4A/bCLnKMXEI9oVWFL2GfLX12VCSqsChP+Kak1rUqJbyhn0fnAhswoo9sI5vRvKk0Azavde02VAT742CSgiCj5eB1MrM6PbiSwR37EyzH9A+kp556+i/0ByDcekiR9l4OAAAAAElFTkSuQmCC>

[image13]: <data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAB0AAAAZCAYAAADNAiUZAAABe0lEQVR4Xu2UvytGURjHH2FQUn4MhIHNRCGLJBkYSBSDwWpQBmE2+AdkkB+L2aAUi8Eok5QMNvkxiKJY5Mf323POvfd96O3lnkn3U5/Oe57n3Pu8PeecK5Lx3xmE63ALXsFT95sxOguro9WBWYGbNggG4Ae8hI0ml4pyeAQnTZy0wxf4KdqVYDTDO9hqE2BUtOAb7Da5VAyLvrjSxIvgqsudSMC99a3li5MUwwv4JIHbSnxrWZSnl17Dd7gAK+Kl4fCtPbcJQ5/oSa5zI+eEW9AlevppQSyLFt2xCcOi6Dp6CGtdfAwewCon53nxrX2FnSZnmRa9PpYbyX32Vn6+BRHJ1taYnIVFp+A27BFtK+EdTv4ZzocS829siBbdFz3F+ZiHS6LX5hjOufivixZKKeyVuBvcX1/s2Y2eR9ifmP8Z/ylcc3MWfYAt8F5y77GPp6YJ7sJ6WCJ60vdgmRtn4qVRPAgjop9EngPuaYOLd8AzOA4nYJuLZ2SE5ws3BU58HObtPQAAAABJRU5ErkJggg==>

[image14]: <data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAB0AAAAZCAYAAADNAiUZAAABhElEQVR4Xu2UPShHURjGH7EoKTEQFilRMsgmSQYGEjaDyUcWgzAbZMcgH4vZoBQ2kslkkcEmJYPBwCIfz9N7jnud/P/oXovur37de9/33HPufe97LpDx3+ml63SLXtNzd66YnKblH6NTZpluhkHSQ1/pFa0Ncokoocd0JIiLVvpI32BVSY06ektbwgQZhC34TNuDXCL6YROXBfECuupyZ0jx2/rSauI4hfSSPiDlsgpfWi2q7pU39IXO0dJoaHr40l6EiS9ognW5jp4uWIdXOXX9LYuwRXfCRMAQPaA19JR2u/g87H5vpYvnxJf2ibYFuTh9sAl1FOroe9pIJ2Hb6sfES1sR5OKEi2o/+y2kRUfpBt2GdXxeNFCT7cO6OBcN9I5OuGv/SfQQs3SBFsO21Iwbkwp6gw46TscQlbcTn6ukv9evyp2PZlrvzvWmh7C30yJrfhCih0mM/4Hswkp4AusHoVi1Oy+ie7CHSYwmW6FL9IhOIWqYAdivchjWI9pSGRl/wztXn06ugS6TWgAAAABJRU5ErkJggg==>

[image15]: <data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAB0AAAAZCAYAAADNAiUZAAABYklEQVR4Xu2UPS8EURSGjxCJRDbxUdKIZEMiCtGJSkFBYUs1Eo3OPxA9ovDxDxQqdERUKo0odCIRhUJBIz7eN+cee3OykyX3VjJP8mRm3zM7Z/bsvSNS8t+ZgbvwAN7D63DOjK7Cnp+rM7MJ930IpuEnvIP9rpZEJzyHCy4nY/AVfolOJRsD8BGO+gKYF234DidcLYk50Rt3ubwFbofalWT8b220vHFMK7yFL5J5rMRGy6ZcvfQBfsA1WKlfmg8b7Y0vNGBYdJXz2Cinv2JdtOmhLzhq8AT2wUs45fLuULO8EBvtGxx3tZhZ0QfjkXBFP8MhlxPLC4lH2+tqMb4p97NtId+06dbaE/3SsegqLqIKn+By+Gx/CZvFOfEPkQT37CRcgotSH6PlO6HWdLx/YQQOhnP+0lPY4XJieTL2AjkSfStdiK4HYnl7qFmeTBvcghvwDK6IjpVYzsXFmuUlJfn5BiMaUCAQIUG8AAAAAElFTkSuQmCC>

[image16]: <data:image/png;base64,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>

[image17]: <data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAkAAAAaCAYAAABl03YlAAAAjElEQVR4XmNgGAXkAkYgFoZiDMAJxLlA/AyKnwCxLYoKIDgExFeAWAXKNwHiPUDMDVOQA8RTGCBWwYA8EPPBOIJAfBqIPeDSWIA+EH8CYiV0CWRgDMRfgZgHXQIZgNwxgQHhYJiYExB7I4kx8APxeyBeCMQrgfghEM9FVgADIOskgVgciFnR5EYBEQAAAK8QUyUGpugAAAAASUVORK5CYII=>

[image18]: <data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAcAAAAeCAYAAADgiwSAAAAAo0lEQVR4XmNgGOpABV0ABhSBeCG6IAy0ArELuiAIcADxViCWRhbMA+L/aPgnEFuCJHmAWBKII4D4H5QtBsTMIEkYKGeA6MIALEC8Boifo0uAgDgQ3wXiA2jiYGADxL+BeBK6BAgUMUDsC2KAWAFynDBIAmbfWyDWBGJjIF4MxJxgbQyQIHvIAPHGKiA2g0mAgC8QfwTiDUDsiSwBA7DAGAXIAAD8ORoJ0Ewr5QAAAABJRU5ErkJggg==>

[image19]: <data:image/png;base64,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>

[image20]: <data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAIoAAAAZCAYAAADudbaJAAAFCUlEQVR4Xu2ZbejdYxjHv7IVGXmKKcvf45LHxZSnlEYUWqitEK8IL5Q32mut5o2E0hiakiihpSTZCcm2MoqtRCEPodEWasy4Pq772rnPvfM7v/N/2Dl/f/e3vp1z3/fv3E/X976u6/4dqaKioqKiYtbgIOMliXyvqOiLs4w/JfJ9tgHxXmE80Xhw0RY4O/GQsmGO4jjj18afjR3jgp7WA4THjX8nPlK0jRtLjduN641bje/JBRM4zPio8cXEHcaHsva5ikONz8ht1tEIhDJPLo4Ljb/JB54N4eci4y/GdfI5shEd+fwQDbjYuNu4OpUDCIe2uY4L5DbraARCYbDT5MbAKBgCFz5ubDDuNS7L6i41Pmw8I5URBPO9Zd8Tjru0v3jmIkYmlBBHeBAEwikmFI3bq7AB36g3zOTIPcz1vU3/lt+Sh6UmkOvQ9+nGY+SHpVzzkcYTjMcX9QH6IFfgmabciHramwxJH8ca56fv9Me4TeA55kN/TUJhHfQDsfGE/HdTBgN9mpUZAJEglnF7FQSwxbjE+IpxlzxEHpHa2fyv0nP9hMK6MEA/sM7PjWuN98r736jezd4s7/9p4yfGFeoV0rWp/Vt5KPxLnhuFQY42PmX8LrXz+UKqBw+omxdibLwlySmHg77eT8/leEnexnw+NK4y/q5eoZD0fyQf6zn5BeUL+X5NCcRwksSTi3o2IyaPkIbBcuPNk2QbmAOh55qsjhATCXebUGhr2hzaryvqEAubTZJI2MvHBX/KN58Tys0Qg+Z7h2BItBmTZ8q5A8rUP5HVISLWwAEN4OnoPzwZ4kMkZX+lR4nyfeqKmj7wrk17MRAshEWj6n5AhUw+EslxgPHL0HOZ3GDfa3pC4ZBwMl823m48NWs7T+69eIbfBxkTD3eUfN9eV3O4OVP7zx2EIekrEEK5MauLtcX8Y06n7HvCUQoF4TIu/VGHoKcVctgErpqLyoYETuxkvcpMg/HfNR6e1cXGcCqnI5TwmjmXpjZ+S/l545MF18jzB4wLmxBhqRw/PAXzD4RQ8jWUQok5lf2VQgEI7g9117VT7j3Dw0wKg7wJiBdwDIRXGQfiVOR5Q2wMbW3JbEfNCSTgkHA7wi3/Ks8hSGgxcj+j5GgTSpNQc3EHZloogOT8VvnLOH6323h51j40BnkTgPoelA/CRNrACQkFD8s2MG6ZkHLqSd5wwyCu8xg8x/3yq3MTlhVl3HP0Q9jYocGelEM26FZFH8yRkJEjDEv/gWGEEr9rCz3nGB9TNyRixzvkYXbQfvQFyiqN1kZC1ahxg3yBq+QLxpjPym9kvIwDbM6b8qw+MJHKJKVNwCj0m/8dQDIaf1+cJB+H/74CiPKmrMzc3jEuTGXmtFF+JQWfGT/I2vmkzI2F/gFC2yDf4/xd0LnGH42Lszr6f1vdWxNgfH7LermlhnCYa6z/fLm3vCqVh0IksaUQ2hjZ/iiBOO6RexBuJOQrGI/QkIO4z1XyzkSuvZz2QUAobxi3ya+/GKDslxO4Ry6gjvE29cb5K+W3RgSzSW60iawdYdA386edz7Xqfz0ORsjK6zrqhhWu7OQceKHX5B4wQjFEKB/L9+tL46vGH4x3a4o5yn8JxFs28Go1u3o8AydypfytbdumhGvGALj38AIl4mVZ0ws3xmF+Ze6QI16QTev2kSFeAvIZfTN/9oBy7BFzn8lxKyoqKioqKioqKioqKir+T/gHu+dqKr8aYqYAAAAASUVORK5CYII=>

[image21]: <data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAA8AAAAbCAYAAACjkdXHAAAA90lEQVR4XmNgGAUjDfCg8QWwiGEFQUB8EYhXA7E+EB8C4hNA/AyIGZHUYQB+IF4DxCpA/B+IXwOxFRBbA/FHBgK2uwDxLAaIDSDNGVDxXiifBcrHCjyB2A6IeYH4ORArQcX5gNgepogBYngAEDsjicGBJhBvBWIOdAko4AbiXUBcgy4BApMYIK4gGYAC5QADwsnIAOTcVCDeDsRbGLAEIEgTyL/YnKwIxFlAbALEV4FYHlWagcEBiP+iC0KBLgMkPMoZIFGKEfrMQCyMLogEYN6KRhMnChQxQGxVBeIyNDmCIAyINwPxBAYsfiYGgJIxK7rgKCABAAByHR8zRgDRlwAAAABJRU5ErkJggg==>

[image22]: <data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAEMAAAAWCAYAAACbiSE3AAAAp0lEQVR4Xu2XIQ7CUBBElxAEAYcqDoXD4nsCJJL0ApwGzQFIOABnqEIiKzhHZ/LTENaUBDnzkmf+pGbz908aYYwx5i9WsMqHqkzhE97hNmXSTOAetrCOMih5dvABX/CYMlm4NlyfBs5TJssVvuEJzr4jHdgyF3iGi5TJsInPbeCKyMJ3glV7CNGVYKUODcJala3UdZRBcCDGGDMG/1q7H73BZfnMGAV6cpoYgQRdqg0AAAAASUVORK5CYII=>

[image23]: <data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAGcAAAAXCAYAAAAWY1E4AAACTUlEQVR4Xu2YO2hUURCGf/EBokR8IAlaJCKKgliIpLEIFkERRcQiIKiND2wURG1sLCxEYuEL8YEg2IhFII3YJFhahDRiYaWIYmGhoIXi4/+Zc7JnJ7hu2M0K3vngg907l124c8/MnAMEQRAEwb9hJ71N79E3dDJ91jV5ki6fujvoKBvofvqQvqaH03d5lX6jX+iudH+n6adH6TwfqBJKxF1/keygP+krH5hl5sJeiKd0o4tVijX0Pd3sA2Qf/UW/+8AssJC+o/dpn4tVlj2wBCx11+fQ6yn23MXaSRc9Tz8g+ts0LsIS4FkLe5PVd3a7WLu4DEuKkqMkBQWL6TimJ0c1/yX9BJvo2s0y2KrUNLjIxYJE7jdKjkZp+Zb+oGdR/zZraJBlg94OGxp6kvreDN30ER2i810sSOSS9tgHCtSclbDMOfoE1sBP0FN0L+0t7pkJSuoV2H/EKkqUJe1YfagO9Zuy7GmC+wjbIx2nW4pYK2iVKkGXEIPBVEn7Sre6WIlPzgHYaL0NlpxD9A59AJvwWkErR31IQ8INF6sUeYR+QVe4WMl62MPK5FKopJ2hF2AlTm/76eK+VtDvHYTteVa72H+NNpufYQ+4tFG914q4CTtKOYJaWRtAfWJ11NOuMhc0yabis1ZOHgiUjFtFLCct6BB5cFgAK13PYCVRjNBV6bMOJ0dhSQs6hB76NdggMAYbn3Pj1witzaROsTUUNNsf1tEJ1PZWjRxGxU+l/4ZODAbpEh+ArSYNBzMpZ/q9lahtXhuplRsEQfAHfgNpsW6m5KnlFgAAAABJRU5ErkJggg==>

[image24]: <data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADgAAAAWCAYAAACVIF9YAAADj0lEQVR4Xu2X38uNWRTHv0KRERl5TX4mppSihBskP4rEBYriYpobLtwglFL+ABdyo4SiKD9LEko6IZQiNSJRTNNM1MxESBSzPmftdc5+nvM8jpsJ9X7r23v23utZ+7vXXnvt/Uq96MXXQo/xh3JnCQPKHd8L5hvPGO8bfzX2LQ43Mcx4odzJiicbJ5UHSiByY439ywNdgJDRxqnlgRLQgYaqHZho/DP9/tF4y3jW2Kdl4fM8Nu7L+rTX+F5uDLeqcwEj5ZF7bWwY/zauVtF5HbD713jXeFjuB385Bqmo45U6dexK/YG1xnPGv4y/J/5j/M04IoyGyCefGR2Gt8Ztaosn8reNj4zjUv8vxg/GlcmmDthil/vD12X53IC/J1XUQSqGDsCOnjc+S20ww3goawN2dk40iBoT7WgNO4jyJ+P61H6S2itaFp6qDePHrK8Km43XjIOzvmVyfwhGOBpo1+kA/YynVFzgbOORrL1c7rOFn+QfbM875amC46PyqL9JbYQFIqIhoA6IbKhY9RbKA/OHPDvQgJ86HbHzG+W7GsCePkBmXVcxE7susCEX9n8tEL/T1X2B8S3BeCA/lyyEzKCP9kHjlmTXQl2KsnM4phQPV3WKknJMQH9VxQtUpSh++I5FstiqFGXXQgcaAuPlheShcVrqW2c8rs7C2MRAFQ83jllYvoMc3BtqFxkc7ZZXrW47CMpF5mbqix1EA2cpdGC3SW0dn7vUGcsLHQWGdszVxB55bnMm2K2dcsdRBABl/USyI3oc6GPJrhu4Jp4bX8iDsljuh6uGuxeQTaEDDZyt0FGXISyCwMXOEaiLqgkIFySpgDHnpupMAMaxjSr6JQsMDJV/F1W0kdo58B2CQ0cdZsmzITBBficWgDNeF/mWNuTpMiW1ie5S+UsiMMr4VH7ou2GRPLoBAofwqIBoWKCijgggOqrAsbkq9x0g3Un7ApYY76h98/OO4yXAWQtQKBB0Wi6UhXIeee/FZQ0OJLt5WV+P/HVCMQHs0DsVCxYa+C50oOGKOnUE4r7L5wZkIOe2kNIY4Wi/PCWIWPkZxST35NUQO84Ji2FncxDNl/LrJ8COcNbwzbfMxSslBxoIVuhAwyV16gh03HcZuCryTGuCreVdt8b4c2ksQBrNlduNKY11A/ar5N/iowrsbOhAQ6EKZuBFs6HcmQGdFCneu98tqv5FykExy7OoF734FvEfg2PZxOtgPmAAAAAASUVORK5CYII=>

[image25]: <data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACEAAAAWCAYAAABOm/V6AAACVUlEQVR4Xu2VzauOQRjGL6EI+YxEkWzkO2FlI8SClKTkr6AIa9lZiAglSsrHDllYvKWkFBFZoLCgyEaxxPU798z7zjM9j97FURbnql/nzD3zzNxfM680pn+vGWZ8baw0qTaMliaac+a5uWFmNaf7mm52lQa8nl0a/qJxZq6Zr/ZID5lXZp45at6a3QrnEN+vMs/MhGQb0Wrz23wyHytemJVp3WLzyLw0D80bsybNoTkKB66k8RLTM2cUzrDfZ8Wer9OavrYonGjjgSJ1RM/ht8xkRRZOmncK59A688McSeOp5qZZlsaIb7E1SoFO1AbrrtlXjHvmm5obUpIPiujJAhn9bo6l+WmKviAjaK0igwvTuKH91Zi6ndegjog0ciAHZxFpTxE9WSBjj801xR4rzO20Dt1RSwa6tFGR/lIc1OUEZduZbAT01WwwF8yBZEdnVTVjl7glRFPrl4ZzggxQRprwuAbZpBStZaiV00nz1eKgYZyohVOHFVlAyxUO7lFc4VPJ3td2RcS5sUoNW45alCU3Iw5xXZ+YmYq35P5gaei0YsOD9YTaG5Pu54CfZn1hzyKz3LLcjByMAz0NmvVS+juivGFXVHzM9eMaZuUr+t4sKOxZuQy5GfP6ngZONJ6HfMe7nFiqeFEvK5qN1FJTysRtqkUWtlY2frDIDMESdH7M+uIR4jHCiR3lRKHN5ou5bi4qyrBX4VCp3Iy1HVEaguENoTmvlpN8sEnxhLf9MGVNMdsS/N8mruO92pjE3jjIc//ULGpOj54oV5eDWZSifJHH9H/oD+dOf9L35L2SAAAAAElFTkSuQmCC>