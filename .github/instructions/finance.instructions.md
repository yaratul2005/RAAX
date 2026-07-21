# Finance Module & Monetary Instructions

## applyTo: "modules/Finance//*.php"

* **Currency Precision**: Never use floating-point or decimal data types for monetary fields. All calculations must use integer cents (e.g., $100.00 is stored as 10000 cents).

* **Currency Coupling**: Every monetary field must be stored alongside an ISO 4217 three-letter currency code column (`currency_code`).

* **Double-Entry Integrity**: Every ledger posting must be wrapped in an ACID-compliant transaction verifying that debits equal credits:
  - ∑Debits = ∑Credits

* **Audit Trails**: All transactional ledger models must implement `SoftDeletes` and commit append-only, tamper-evident cryptographic hash chains:
  - H_n = SHA-256(H_{n-1} ∥ Payload_Hash_n)
