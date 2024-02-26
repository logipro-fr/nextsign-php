<?php

namespace NextSignPHP\Tests\Domain\Model\DTO;

use NextSignPHP\Domain\Model\DTO\SignerDraft;
use PHPUnit\Framework\TestCase;

class SignerDraftTest extends TestCase
{
    public function testSerialize(): void
    {
        $lastname = "a";
        $firstname = "b";
        $email = "c";
        $phone = "d";
        $target = [
            "lastName" => $lastname,
            "firstName" => $firstname,
            "email" => $email,
            "phone" => $phone
        ];
        $signer = new SignerDraft($lastname, $firstname, $email, $phone);

        $this->assertEquals($target, $signer->jsonSerialize());
    }
}
