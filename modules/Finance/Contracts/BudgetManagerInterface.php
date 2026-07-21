<?php

namespace Modules\Finance\Contracts;

interface BudgetManagerInterface
{
    public function checkFunds(string $accountId, int $requestedAmountCents): bool;
    public function encumberFunds(string $accountId, string $sourceType, string $sourceId, int $amountCents): \Modules\Finance\Models\EncumbranceLedger;
    public function relieveFunds(string $sourceType, string $sourceId, int $amountToRelieveCents): void;
}
