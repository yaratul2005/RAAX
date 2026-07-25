<?php

namespace Tests\Feature;

use Tests\TestCase;
use Modules\Finance\Services\NbrQrCodeSignerService;
use Modules\Inventory\Services\ZplLabelGenerator;
use Modules\Procurement\Services\DynamicReorderEngine;

class EnterpriseUpgradeTest extends TestCase
{
    public function test_nbr_qr_code_signer_generates_valid_ecdsa_payload()
    {
        $result = NbrQrCodeSignerService::generateNbrQrPayload(
            '1899201928301',
            'SO-2026-4412',
            '2026-07-25',
            73913000,
            11087000
        );

        $this->assertEquals('1899201928301', $result['bin']);
        $this->assertEquals('SO-2026-4412', $result['invoice_number']);
        $this->assertNotEmpty($result['digital_signature']);
        $this->assertStringContainsString('NBR_6.3::', $result['full_qr_string']);
    }

    public function test_zpl_label_generator_outputs_valid_printer_code()
    {
        $zpl = ZplLabelGenerator::generateZplBinLabel('SKU-FASTENER-A', 'Fastener', 'BIN-MAIN-A1', 4500);
        $this->assertStringContainsString('^XA', $zpl);
        $this->assertStringContainsString('^XZ', $zpl);
        $this->assertStringContainsString('SKU-FASTENER-A', $zpl);
    }

    public function test_dynamic_reorder_engine_calculates_optimal_rop()
    {
        $rop = DynamicReorderEngine::calculateReorderPoint(50, 7);
        $this->assertEquals(50, $rop['avg_daily_usage']);
        $this->assertEquals(7, $rop['supplier_lead_time_days']);
        $this->assertGreaterThan(350, $rop['dynamic_reorder_point']);
    }
}
