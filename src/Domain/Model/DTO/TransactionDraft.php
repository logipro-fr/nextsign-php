<?php

namespace NextSignPHP\Domain\Model\DTO;

class TransactionDraft
{
    public function __construct(
        public readonly TransactionId $id,
        public readonly string $editorUrl
    )
    {
        
    }
}