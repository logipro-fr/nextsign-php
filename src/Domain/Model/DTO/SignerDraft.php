<?php

namespace NextSignPHP\Domain\Model\DTO;

use JsonSerializable;

class SignerDraft implements JsonSerializable
{
    public function __construct(
        public readonly string $firstname,
        public readonly string $lastname,
        public readonly string $email,
        public readonly string $phone
    ) {
    }

    public function jsonSerialize(): mixed
    {
        return [
            "lastName" => $this->lastname,
            "firstName" => $this->firstname,
            "email" => $this->email,
            "phone" => $this->phone
        ];
    }
}
