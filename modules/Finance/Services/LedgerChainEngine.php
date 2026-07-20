<?php

namespace Modules\Finance\Services;

use App\Services\Tenant\TenantContextManager;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Modules\Finance\Models\JournalEntry;
use Modules\Finance\Models\LedgerChain;

class LedgerChainEngine
{
    protected TenantContextManager $tenantManager;

    const GENESIS_HASH = '0000000000000000000000000000000000000000000000000000000000000000';

    public function __construct(TenantContextManager $tenantManager)
    {
        $this->tenantManager = $tenantManager;
    }

    public function hashAndChainEntry(JournalEntry $entry): LedgerChain
    {
        $tenantId = $this->tenantManager->getTenantId();
        if (!$tenantId) {
            throw new InvalidArgumentException("Tenant context required.");
        }

        // Fetch previous sequence
        $previousChain = LedgerChain::where('tenant_id', $tenantId)
            ->orderBy('sequence_number', 'desc')
            ->lockForUpdate() // Prevent race conditions
            ->first();

        $sequenceNumber = $previousChain ? $previousChain->sequence_number + 1 : 1;
        $previousHash = $previousChain ? $previousChain->chain_hash : self::GENESIS_HASH;

        // Canonicalize payload
        // We load lines and sort them by ID to ensure consistent serialization order
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

        $payloadHash = hash('sha256', $payload);
        $chainHash = hash('sha256', $previousHash . $payloadHash);

        return LedgerChain::create([
            'id' => Str::uuid()->toString(),
            'tenant_id' => $tenantId,
            'journal_entry_id' => $entry->id,
            'sequence_number' => $sequenceNumber,
            'payload_hash' => $payloadHash,
            'chain_hash' => $chainHash,
        ]);
    }
}
