<?php

namespace NextSignPHP\Domain\Model\DTO;

class User {
    public function __construct(
        public readonly string $accountId,
        public readonly string $contractorName,
        public readonly string $contractorUserId,
        public readonly string $contractorEmail
    )
    {
        
    }
}