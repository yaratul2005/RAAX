<?php

namespace Modules\Finance\Services;

use Modules\Finance\Models\FinanceInvoice;
use App\Services\Tenant\TenantContextManager;
use Carbon\Carbon;
use InvalidArgumentException;

class AgingAnalysisService
{
    protected TenantContextManager $tenantManager;

    public function __construct(TenantContextManager $tenantManager)
    {
        $this->tenantManager = $tenantManager;
    }

    public function getAgingSchedule(string $type, ?string $evaluationDate = null): array
    {
        $tenantId = $this->tenantManager->getTenantId();
        if (!$tenantId) {
            throw new InvalidArgumentException("Tenant context required");
        }

        if (!in_array($type, ['AP', 'AR'])) {
            throw new InvalidArgumentException("Invalid aging type. Must be AP or AR.");
        }

        $date = $evaluationDate ? Carbon::parse($evaluationDate) : Carbon::today();

        // Get outstanding invoices
        $invoices = FinanceInvoice::where('tenant_id', $tenantId)
            ->where('type', $type)
            ->where('status', '!=', 'paid')
            ->get();

        $schedule = [
            'current' => [
                'total_balance' => 0,
                'invoices' => []
            ],
            '1_30_days' => [
                'total_balance' => 0,
                'invoices' => []
            ],
            '31_60_days' => [
                'total_balance' => 0,
                'invoices' => []
            ],
            '61_90_days' => [
                'total_balance' => 0,
                'invoices' => []
            ],
            '91_plus_days' => [
                'total_balance' => 0,
                'invoices' => []
            ]
        ];

        foreach ($invoices as $invoice) {
            $balance = $invoice->outstanding_balance;
            if ($balance <= 0) {
                continue;
            }

            $dueDate = Carbon::parse($invoice->due_date);
            $overdueDays = (int) $date->diffInDays($dueDate, false) * -1; // Negative diffInDays means date > dueDate (overdue)

            $invoiceData = [
                'id' => $invoice->id,
                'invoice_number' => $invoice->invoice_number,
                'party_id' => $invoice->party_id,
                'due_date' => $invoice->due_date->toDateString(),
                'outstanding_balance' => $balance,
                'currency_code' => $invoice->currency_code,
                'overdue_days' => $overdueDays,
            ];

            if ($overdueDays <= 0) {
                $schedule['current']['total_balance'] += $balance;
                $schedule['current']['invoices'][] = $invoiceData;
            } elseif ($overdueDays >= 1 && $overdueDays <= 30) {
                $schedule['1_30_days']['total_balance'] += $balance;
                $schedule['1_30_days']['invoices'][] = $invoiceData;
            } elseif ($overdueDays >= 31 && $overdueDays <= 60) {
                $schedule['31_60_days']['total_balance'] += $balance;
                $schedule['31_60_days']['invoices'][] = $invoiceData;
            } elseif ($overdueDays >= 61 && $overdueDays <= 90) {
                $schedule['61_90_days']['total_balance'] += $balance;
                $schedule['61_90_days']['invoices'][] = $invoiceData;
            } else {
                $schedule['91_plus_days']['total_balance'] += $balance;
                $schedule['91_plus_days']['invoices'][] = $invoiceData;
            }
        }

        return $schedule;
    }
}
