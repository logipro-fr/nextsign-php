<?php

namespace NextSignPHP\Domain\Model\DTO;

class User
{
    public function __construct(
        public readonly string $name,
        public readonly string $userId,
        public readonly string $email
    ) {
    }
}
