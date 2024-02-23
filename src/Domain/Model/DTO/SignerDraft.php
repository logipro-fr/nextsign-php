<?php

namespace NextSignPHP\Domain\Model\DTO;

use JsonSerializable;

class SignerDraft
{
    public function __construct(
        public readonly string $lastname,
        public readonly string $firstname,
        public readonly string $email,
        public readonly string $phone,
        public readonly string $userId
    ) {
    }
}
