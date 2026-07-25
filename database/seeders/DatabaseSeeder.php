<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Modules\Finance\Models\LedgerAccount;
use Modules\Finance\Models\JournalEntry;
use Modules\Finance\Models\JournalEntryLine;
use Modules\Procurement\Models\Vendor;
use Modules\Procurement\Models\PurchaseOrder;
use Modules\Procurement\Models\PurchaseOrderLine;
use Modules\Sales\Models\Customer;
use Modules\Sales\Models\SalesOrder;
use Modules\Sales\Models\SalesOrderLine;
use Modules\Inventory\Models\Warehouse;
use Modules\Inventory\Models\WarehouseBin;
use Modules\Inventory\Models\InventoryBatch;
use Modules\HR\Models\Department;
use Modules\HR\Models\Designation;
use Modules\HR\Models\Employee;
use Modules\HR\Models\Shift;
use Modules\HR\Models\AttendanceLog;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $tenantId = 'aca9ea90-0d0f-4ed9-98ed-398af6b67efd'; // Tenant A (HQ)

        // 1. Chart of Accounts
        $accModels = [];
        $accounts = [
            ['account_code' => '1001', 'account_name' => 'Cash in Vault', 'account_type' => 'asset'],
            ['account_code' => '1002', 'account_name' => 'Operating Bank Account', 'account_type' => 'asset'],
            ['account_code' => '1100', 'account_name' => 'Accounts Receivable (AR)', 'account_type' => 'asset'],
            ['account_code' => '1200', 'account_name' => 'Inventory FIFO Account', 'account_type' => 'asset'],
            ['account_code' => '2001', 'account_name' => 'Accounts Payable (AP)', 'account_type' => 'liability'],
            ['account_code' => '4001', 'account_name' => 'Gross Sales Revenue', 'account_type' => 'revenue'],
            ['account_code' => '5001', 'account_name' => 'Office Rent & Administrative Expense', 'account_type' => 'expense'],
            ['account_code' => '5002', 'account_name' => 'Cost of Goods Sold (COGS)', 'account_type' => 'expense'],
        ];

        foreach ($accounts as $acc) {
            $m = LedgerAccount::firstOrCreate(
                ['account_code' => $acc['account_code'], 'tenant_id' => $tenantId],
                [
                    'id' => Str::uuid()->toString(),
                    'account_name' => $acc['account_name'],
                    'account_type' => $acc['account_type'],
                    'currency_code' => 'BDT',
                ]
            );
            $accModels[$acc['account_code']] = $m;
        }

        // 2. Vendors
        $vendor1 = Vendor::firstOrCreate(
            ['tenant_id' => $tenantId, 'name' => 'Global Steel Suppliers Ltd'],
            ['id' => Str::uuid()->toString(), 'email' => 'sales@globalsteel.com', 'phone' => '+8801700000001', 'status' => 'active']
        );
        $vendor2 = Vendor::firstOrCreate(
            ['tenant_id' => $tenantId, 'name' => 'Apex Industrial Components'],
            ['id' => Str::uuid()->toString(), 'email' => 'orders@apexind.com', 'phone' => '+8801700000002', 'status' => 'active']
        );

        // 3. Customers
        $customer1 = Customer::firstOrCreate(
            ['tenant_id' => $tenantId, 'name' => 'Apex Holdings Corp'],
            [
                'id' => Str::uuid()->toString(),
                'credit_limit_cents' => 500000000, // BDT 5,000,000
                'outstanding_balance_cents' => 85000000,
                'status' => 'active'
            ]
        );
        $customer2 = Customer::firstOrCreate(
            ['tenant_id' => $tenantId, 'name' => 'TransGlobal Logistics'],
            [
                'id' => Str::uuid()->toString(),
                'credit_limit_cents' => 200000000,
                'outstanding_balance_cents' => 12000000,
                'status' => 'active'
            ]
        );

        // 4. Warehouse & Bins
        $warehouse = Warehouse::firstOrCreate(
            ['tenant_id' => $tenantId, 'code' => 'WH-MAIN'],
            ['id' => Str::uuid()->toString(), 'name' => 'Central Distribution Facility']
        );

        $bin1 = WarehouseBin::firstOrCreate(
            ['tenant_id' => $tenantId, 'warehouse_id' => $warehouse->id, 'bin_label' => 'BIN-MAIN-A1'],
            ['id' => Str::uuid()->toString()]
        );
        $bin2 = WarehouseBin::firstOrCreate(
            ['tenant_id' => $tenantId, 'warehouse_id' => $warehouse->id, 'bin_label' => 'BIN-MAIN-B4'],
            ['id' => Str::uuid()->toString()]
        );

        // 5. Inventory Batches
        InventoryBatch::firstOrCreate(
            ['tenant_id' => $tenantId, 'item_sku' => 'SKU-RAW-STEEL'],
            [
                'id' => Str::uuid()->toString(),
                'warehouse_bin_id' => $bin1->id,
                'original_qty' => 1200,
                'remaining_qty' => 1200,
                'unit_cost_cents' => 4500, // BDT 45.00
                'currency_code' => 'BDT',
                'created_at' => now(),
            ]
        );
        InventoryBatch::firstOrCreate(
            ['tenant_id' => $tenantId, 'item_sku' => 'SKU-FASTENER-A'],
            [
                'id' => Str::uuid()->toString(),
                'warehouse_bin_id' => $bin2->id,
                'original_qty' => 500,
                'remaining_qty' => 150,
                'unit_cost_cents' => 1000, // BDT 10.00
                'currency_code' => 'BDT',
                'created_at' => now(),
            ]
        );

        // 6. Purchase Orders
        $po1 = PurchaseOrder::firstOrCreate(
            ['tenant_id' => $tenantId, 'po_number' => 'PO-2026-8819'],
            [
                'id' => Str::uuid()->toString(),
                'vendor_id' => $vendor1->id,
                'total_amount_cents' => 125000000, // BDT 1,250,000
                'status' => 'sent_to_vendor',
                'created_at' => now()->subHours(2),
            ]
        );
        PurchaseOrderLine::firstOrCreate(
            ['purchase_order_id' => $po1->id],
            [
                'id' => Str::uuid()->toString(),
                'tenant_id' => $tenantId,
                'item_sku' => 'SKU-RAW-STEEL',
                'qty' => 100,
                'unit_price_cents' => 1250000,
                'total_price_cents' => 125000000,
            ]
        );

        // 7. Sales Orders
        $so1 = SalesOrder::firstOrCreate(
            ['tenant_id' => $tenantId, 'order_number' => 'SO-2026-4412'],
            [
                'id' => Str::uuid()->toString(),
                'customer_id' => $customer1->id,
                'subtotal_cents' => 73913043,
                'tax_cents' => 11086957,
                'grand_total_cents' => 85000000, // BDT 850,000
                'status' => 'confirmed',
                'created_at' => now()->subHours(5),
            ]
        );

        // 8. HR Department, Designation, Employees & Shifts
        $dept = Department::firstOrCreate(
            ['tenant_id' => $tenantId, 'code' => 'ENG'],
            ['id' => Str::uuid()->toString(), 'name' => 'Engineering & IT']
        );

        $desig = Designation::firstOrCreate(
            ['tenant_id' => $tenantId, 'title' => 'Senior Operations Specialist'],
            ['id' => Str::uuid()->toString(), 'grade' => 1]
        );

        $shift = Shift::firstOrCreate(
            ['tenant_id' => $tenantId, 'name' => 'Morning Shift A'],
            [
                'id' => 's1000000-0000-0000-0000-000000000001',
                'start_time' => '09:00:00',
                'end_time' => '17:00:00',
                'grace_period_minutes' => 15,
            ]
        );

        $emp1 = Employee::firstOrCreate(
            ['tenant_id' => $tenantId, 'email' => 'a.rahman@raax.com'],
            [
                'id' => 'e1000000-0000-0000-0000-000000000001',
                'first_name' => 'Abdur',
                'last_name' => 'Rahman',
                'department_id' => $dept->id,
                'designation_id' => $desig->id,
                'joining_date' => '2024-01-15',
                'phone' => '+8801800000001',
            ]
        );

        AttendanceLog::firstOrCreate(
            ['tenant_id' => $tenantId, 'employee_id' => $emp1->id, 'date' => now()->toDateString()],
            [
                'id' => Str::uuid()->toString(),
                'shift_id' => $shift->id,
                'check_in' => now()->startOfDay()->addHours(9)->addMinutes(10),
                'status' => 'Present',
            ]
        );

        // 9. Financial Journal Entries
        $je1 = JournalEntry::firstOrCreate(
            ['tenant_id' => $tenantId, 'reference' => 'JE-INV-2026-001'],
            [
                'id' => Str::uuid()->toString(),
                'entry_date' => now()->toDateString(),
                'description' => 'Office Rent & Administrative Supplies Expenses',
                'amount' => 4500000,
                'currency_code' => 'BDT',
            ]
        );

        JournalEntryLine::firstOrCreate(
            ['journal_entry_id' => $je1->id, 'ledger_account_id' => $accModels['5001']->id],
            [
                'id' => Str::uuid()->toString(),
                'tenant_id' => $tenantId,
                'debit_cents' => 4500000, // BDT 45,000
                'credit_cents' => 0,
            ]
        );
        JournalEntryLine::firstOrCreate(
            ['journal_entry_id' => $je1->id, 'ledger_account_id' => $accModels['1002']->id],
            [
                'id' => Str::uuid()->toString(),
                'tenant_id' => $tenantId,
                'debit_cents' => 0,
                'credit_cents' => 4500000,
            ]
        );
    }
}
