<?php

namespace NextSignPHP\Domain\Model\DTO;

use JsonSerializable;

class Signer implements JsonSerializable
{
    /**
     * @param array<SignatureMark> $signature
     */
    public function __construct(
        public readonly string $lastname,
        public readonly string $firstname,
        public readonly string $email,
        public readonly string $phone,
        public readonly string $userId,
        public readonly array $signature
    ) {
    }

    public function jsonSerialize(): mixed
    {
        return [
            "lastname" => $this->lastname,
            "firstname" => $this->firstname,
            "email" => $this->email,
            "phone" => $this->phone,
            "userId" => $this->userId,
            "signature" => [ "marks" => $this->signature ]
        ];
    }
}
