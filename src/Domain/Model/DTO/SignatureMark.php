<?php

namespace NextSignPHP\Domain\Model\DTO;

use JsonSerializable;

class SignatureMark 
{
    public function __construct(
        public readonly string $type,
        public readonly int $page,
        public readonly int $left,
        public readonly int $top,
        public readonly float $width,
        public readonly float $height
    )
    {
        
    }
}