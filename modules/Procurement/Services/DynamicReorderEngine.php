<?php

namespace Modules\Procurement\Services;

class DynamicReorderEngine
{
    /**
     * Calculate dynamic safety stock level and suggested reorder quantity.
     */
    public static function calculateReorderPoint(int $avgDailyUsage, int $supplierLeadTimeDays, float $serviceFactor = 1.65): array
    {
        // Standard Inventory Formula:
        // Safety Stock = ServiceFactor * (StdDevUsage * sqrt(LeadTime))
        // Reorder Point (ROP) = (AvgDailyUsage * SupplierLeadTimeDays) + SafetyStock
        
        $demandDuringLeadTime = $avgDailyUsage * $supplierLeadTimeDays;
        $safetyStock = (int) ceil($serviceFactor * sqrt($supplierLeadTimeDays) * ($avgDailyUsage * 0.25));
        $reorderPoint = $demandDuringLeadTime + $safetyStock;
        $economicOrderQty = (int) ceil($reorderPoint * 1.5);

        return [
            'avg_daily_usage' => $avgDailyUsage,
            'supplier_lead_time_days' => $supplierLeadTimeDays,
            'demand_during_lead_time' => $demandDuringLeadTime,
            'safety_stock_buffer' => $safetyStock,
            'dynamic_reorder_point' => $reorderPoint,
            'suggested_po_qty' => $economicOrderQty,
            'status' => 'OPTIMAL'
        ];
    }
}
