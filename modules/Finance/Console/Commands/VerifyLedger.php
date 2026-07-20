<?php

namespace Modules\Finance\Console\Commands;

use Illuminate\Console\Command;
use Modules\Finance\Models\LedgerChain;
use App\Services\Tenant\TenantContextManager;
use Modules\Finance\Services\LedgerChainEngine;

class VerifyLedger extends Command
{
    protected $signature = 'raax:ledger:verify {tenant_id}';
    protected $description = 'Verify the cryptographic integrity of the general ledger chain for a specific tenant.';

    public function handle(TenantContextManager $tenantManager): int
    {
        $tenantId = $this->argument('tenant_id');
        $this->info("Verifying Cryptographic Ledger Chain for Tenant: {$tenantId}");

        $tenantManager->setTenantId($tenantId);

        $chains = LedgerChain::where('tenant_id', $tenantId)
            ->with('journalEntry.lines')
            ->orderBy('sequence_number', 'asc')
            ->get();

        if ($chains->isEmpty()) {
            $this->info("No ledger entries found for this tenant.");
            return 0;
        }

        $previousHash = LedgerChainEngine::GENESIS_HASH;

        foreach ($chains as $chain) {
            $entry = $chain->journalEntry;

            if (!$entry) {
                $this->error("CRITICAL TAMPER DETECTED: Journal Entry for Chain Sequence {$chain->sequence_number} has been deleted.");
                return 1;
            }

            // Recalculate payload hash
            $lines = $entry->lines()->orderBy('id')->get()->map(function ($line) {
                return [
                    'id' => $line->id,
                    'account_id' => $line->account_id,
                    'debit_cents' => $line->debit_cents,
                    'credit_cents' => $line->credit_cents,
                ];
            })->toArray();

            $payload = json_encode([
                'journal_id' => $entry->id,
                'date' => $entry->date->toDateString(),
                'lines' => $lines
            ]);

            $expectedPayloadHash = hash('sha256', $payload);

            if ($expectedPayloadHash !== $chain->payload_hash) {
                $this->error("CRITICAL TAMPER DETECTED: Payload Hash mismatch at Sequence {$chain->sequence_number} (Journal ID: {$entry->id}). The row data was modified.");
                return 1;
            }

            $expectedChainHash = hash('sha256', $previousHash . $expectedPayloadHash);

            if ($expectedChainHash !== $chain->chain_hash) {
                $this->error("CRITICAL TAMPER DETECTED: Chain Hash mismatch at Sequence {$chain->sequence_number} (Journal ID: {$entry->id}). The cryptographic chain is broken.");
                return 1;
            }

            $previousHash = $expectedChainHash;
        }

        $this->info("Verification Complete: The ledger chain is cryptographically intact.");
        return 0;
    }
}
