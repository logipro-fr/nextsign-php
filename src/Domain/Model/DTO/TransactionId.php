<?php

namespace NextSignPHP\Domain\Model\DTO;

class TransactionId{
    public function __construct(public readonly string $id)
    {
    }
}