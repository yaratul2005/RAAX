# RAAX ERP — Comprehensive End-User Operational Guide

Welcome to the **RAAX Enterprise Resource Planning Platform (Desktop Commercial Edition)**. This guide provides complete operational documentation for end-users, finance teams, warehouse clerks, HR managers, and system administrators.

---

## 1. Getting Started & Launching

### 1.1 Launching the Desktop Software
- **Native Executable**: Launch **`RAAX_ERP.exe`** directly from the root installation folder or your desktop shortcut.
- **Single-Instance Guard**: The system uses a native Win32 Mutex (`{RAAX-ERP-ENTERPRISE-MUTEX-2026}`) to prevent duplicate process instances. Double-clicking while running will bring the active instance to the foreground.
- **Auto-Configured Backend**: `RAAX_ERP.exe` automatically scans for an open HTTP port starting at `8000`, displays a native Win32 loading splash screen, and manages the background server process cleanly.

### 1.2 Tenant & Branch Switching
- Locate the **Company Selector Dropdown** in the top bar.
- Switch between **Tenant A (RAAX HQ)**, **Tenant B (RAAX Chittagong Branch)**, and **Tenant C (Holding Corp)**.
- Data separation is strictly enforced at the database level via **PostgreSQL Row-Level Security (RLS)**.

---

## 2. Keyboard Shortcuts Quick Reference

RAAX ERP is designed for high-speed, keyboard-first operation.

| Shortcut | System Action | Description |
|---|---|---|
| **`Ctrl + N`** | **New Transaction Record** | Opens the Header-Detail transaction entry modal from anywhere in the app. |
| **`Ctrl + F`** | **Global Search** | Focuses the global search bar for instant lookup across POs, Invoices, SKUs, and Asset IDs. |
| **`Ctrl + B`** | **Toggle Sidebar Width** | Collapses/expands the sidebar navigation between full width and icon-only mode. |
| **`Ctrl + S`** | **Save & Post** | Saves the active form/drawer transaction and posts entries to the General Ledger. |
| **`Esc`** | **Close Modal / Drawer** | Dismisses active side drawers or modal overlays immediately. |
| **Double-Click** | **Inspect Master Record** | Double-clicking any row in a data grid opens the Master-Detail Inspection Drawer. |
| **Right-Click** | **Windows Context Menu** | Context menu with options: *Open & Edit*, *Duplicate Record*, *Print Document*, *Audit History*, *Soft Delete*. |

---

## 3. Core Module Operational Workflows

```
                                  +---------------------------------------+
                                  |    RAAX ERP 13 COMMERCIAL MODULES     |
                                  +-------------------+-------------------+
                                                      |
    +------------------+------------------------------+------------------------------+------------------+
    |                  |                                                             |                  |
+---v----+         +---v----+                                                    +---v----+         +---v----+
| Sales  |         | Proc.  |                                                    | Finance|         | NBR VAT|
| & Inv. |         | & POs  |                                                    | & GL   |         | Engine |
+--------+         +--------+                                                    +--------+         +--------+
```

### 3.1 Sales & Invoicing (`modules/sales.blade.php`)
1. **Creating a Sales Order**: Press `Ctrl+N` or click `+ New Sales Order`. Select customer name and populate line items.
2. **Customer Credit Check**: The system automatically verifies customer outstanding balance against approved credit limits (e.g. Apex Corp limit: BDT 2,000,000). Orders exceeding the limit route to the **Approval Queue**.
3. **Quotation Conversion**: Click **Quotation Converter** to convert draft quotations (`QTN-9901`) into confirmed Sales Orders.
4. **NBR Mushak 6.3 Tax Invoice**: Click **Mushak 6.3** on any confirmed order row to generate the statutory tax invoice complete with NBR pipe-separated payload and encrypted ECDSA QR code.

### 3.2 Procurement & POs (`modules/procurement.blade.php`)
1. **Purchase Orders Directory**: View all active purchase orders, vendor names, and approval statuses.
2. **Price Tolerance Enforcement**: If line item prices exceed master supplier rates by >10%, the system flags the PO for manager sign-off.
3. **3-Way Matching Inspector**: Click **3-Way Match Check** to run an automated cross-check between the Purchase Order, Goods Received Note (GRN), and Vendor Invoice before issuing payment.

### 3.3 Inventory FIFO & Bins (`modules/inventory.blade.php`)
1. **FIFO Stock Batching**: View stock items sorted by FIFO batch date, original quantity, remaining balance, and unit cost.
2. **Inter-Bin Stock Transfer**: Click **Transfer Stock** to move items between bin locations (e.g. `BIN-MAIN-A1` $\rightarrow$ `BIN-MAIN-B4`).
3. **Zebra ZPL Label Printing**: Click **Print ZPL Label** to send raw ZPL II thermal barcode label scripts directly to Zebra printer queues.

### 3.4 General Ledger & FX (`modules/finance.blade.php`)
1. **Double-Entry Balance Guard**: When creating manual journals, the system enforces double-entry balance ($\sum \text{Debits} = \sum \text{Credits}$).
2. **SHA-256 Ledger Verification**: Click **Verify Chain** to scan all general ledger records against their cryptographic SHA-256 hashes to verify 100% tamper-free integrity.
3. **SWIFT MT940 Bank Reconciliation**: Import bank statement files (SWIFT MT940 / CSV) via `/api/v1/finance/bank-reconciliation/mt940` to auto-match statement lines against AR/AP ledger entries.

### 3.5 NBR Bangladesh Statutory VAT (`modules/vat.blade.php`)
1. **Mushak 6.1 (Purchase Register)**: Ingests all vendor purchases for input tax credit claims.
2. **Mushak 6.3 (Sales Tax Invoice)**: Dispatches customer invoices with 15% VAT calculation.
3. **Mushak 6.6 (VDS Certificate)**: Click **Issue Mushak 6.6 VDS** to generate withholding tax certificates for supplier payments.
4. **Mushak 9.1 (Monthly VAT Return)**: Click **Export Mushak 9.1 Return** to compile Parts 1–12 of the monthly NBR VAT return.

### 3.6 HR & Payroll Engine (`modules/hr.blade.php`)
1. **Employee Master Directory**: View active employees, department designations, and contact profiles.
2. **Biometric Terminal Attendance**: Click **Sync Terminal** to pull clock-in punches over TCP/IP sockets from ZKTeco & Hikvision terminals (`RAAX_Biometric_Service.exe`).
3. **Payroll Calculation**: Click **Run Payroll Cycle** to compute monthly net salaries (Basic + House Rent + Medical - 10% Provident Fund - TDS Tax Withholding).

### 3.7 Fixed Assets Register (`modules/assets.blade.php`)
1. **Depreciation Engine**: Evaluates asset valuations using **Straight-Line (10% p.a.)** or **Double-Declining Balance (20% p.a.)** methods.
2. **Asset Disposal**: Click **Asset Disposal** to record retired assets and post disposal gain/loss to the General Ledger.

### 3.8 Manufacturing MRP (`modules/manufacturing.blade.php`)
1. **BOM Explosion Tree**: Click **View BOM Tree** to inspect finished goods assembly structures down to raw material components.
2. **JIT MRP Shortfall Calculator**: Click **Run MRP Engine** to calculate raw material shortages against active Work Orders.

### 3.9 EDI Integration (`modules/edi.blade.php`)
1. **ANSI X12 Order Processing**: Inspect inbound EDI 850 Purchase Orders, outbound EDI 855 Acknowledgements, and EDI 856 Advanced Ship Notices.

### 3.10 Audit Trail Logs (`modules/audit.blade.php`)
1. **JSON Before/After Diff Inspector**: Inspect exact record state mutations showing user ID, physical IP address, timestamp, and modified JSON attributes.

### 3.11 Devices & Peripherals (`modules/devices.blade.php`)
1. **Thermal Receipt Printing**: Click **Test Print Receipt** to send raw ESC/POS binary buffers via Windows `winspool.drv` native API (`RAAX_Native_Hardware.dll`).
2. **Barcode Scanner Listener**: Click **Listen Scanner** to receive USB HID barcode scanner input.
3. **Hardware Telemetry**: Query native system CPU, RAM, and DLL diagnostics.

---

## 4. Hardware Configuration & Setup

### 4.1 ESC/POS Thermal Receipt Printers
- Connect printer via USB or Windows Spooler.
- Ensure printer driver is installed in Windows (`Printers & Scanners`).
- Native spooling uses `winspool.drv` bindings (`OpenPrinter`, `StartDocPrinter`, `WritePrinter`).

### 4.2 Zebra / TSC ZPL Thermal Label Printers
- Set thermal printer output to raw text mode.
- ZPL payload format (`^XA ... ^XZ`) is sent directly over standard Windows RAW print queues.

### 4.3 Biometric Terminals (ZKTeco / Hikvision)
- Connect biometric device to local network (Ethernet/Wi-Fi).
- Ensure terminal IP is reachable on TCP port `4370`.
- Launch `RAAX_Biometric_Service.exe` to run background punch listener daemon.

---

## 5. System Support & Troubleshooting

- **Database Connection Issues**: Verify PostgreSQL 16 service is running on `127.0.0.1:5432`.
- **License Key Verification**: System includes valid unlimited commercial license (`RAAX-DEV-UNLIMITED-LICENSE-KEY`).
- **Log Inspection**: System logs are written to `storage/logs/laravel.log` and accessible via **Audit Trail Logs** in the sidebar.
