<?php

namespace Modules\Inventory\Services;

class ZplLabelGenerator
{
    /**
     * Generate Zebra Programming Language (ZPL II) code for 2"x1" SKU Bin Labels.
     */
    public static function generateZplBinLabel(string $sku, string $itemName, string $binLabel, int $unitCostCents): string
    {
        $costFormatted = 'BDT ' . number_format($unitCostCents / 100, 2);
        
        return "^XA\n" .
               "^FO50,30^A0N,30,30^FDRAAX ERP - BIN LABEL^FS\n" .
               "^FO50,70^A0N,25,25^FDSKU: {$sku}^FS\n" .
               "^FO50,105^A0N,20,20^FD{$itemName}^FS\n" .
               "^FO50,130^A0N,20,20^FDBIN: {$binLabel} | COST: {$costFormatted}^FS\n" .
               "^FO50,160^BY2,3,50^BCN,50,Y,N,N^FD{$sku}^FS\n" .
               "^XZ";
    }

    /**
     * Generate TSPL (TSC Printer Language) code for thermal receipt printers.
     */
    public static function generateTsplReceiptLabel(string $sku, int $qty): string
    {
        return "SIZE 40 mm, 30 mm\n" .
               "GAP 2 mm, 0 mm\n" .
               "DIRECTION 1\n" .
               "CLS\n" .
               "TEXT 20,20,\"3\",0,1,1,\"RAAX ENTERPRISE\"\n" .
               "TEXT 20,50,\"2\",0,1,1,\"SKU: {$sku}\"\n" .
               "BARCODE 20,90,\"128\",40,1,0,2,2,\"{$sku}\"\n" .
               "PRINT 1,{$qty}\n";
    }
}
