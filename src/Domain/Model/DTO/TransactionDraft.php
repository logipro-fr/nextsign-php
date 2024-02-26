<?php

namespace NextSignPHP\Domain\Model\DTO;

use NextSignPHP\Domain\Model\NextSign\TransactionType;

class TransactionDraft
{
    /** @param array<SignerDraft> $signers */
    public function __construct(
        public readonly string $transactionName,
        public readonly TransactionType $strategy,
        public readonly string $accountId,
        public readonly Document $document,
        public readonly User $contractor,
        public readonly array $signers
    ) {
    }
}
