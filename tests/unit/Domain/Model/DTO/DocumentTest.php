<?php

namespace NextSignPHP\Tests\Domain\Model\DTO;

use InvalidArgumentException;
use NextSignPHP\Domain\Model\DTO\Document;
use PHPUnit\Framework\TestCase;

class DocumentTest extends TestCase
{
    public function testConstruct(): void
    {
        $document = new Document("tests/examples/lorem.PDF");
        $this->assertInstanceOf(Document::class, $document);
    }
    public function testFailConstruct(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("type pda is not recognized");
        $document = new Document("test.PDA");
    }
}
