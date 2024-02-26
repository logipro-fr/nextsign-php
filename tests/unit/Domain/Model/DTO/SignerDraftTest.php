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
        $userId = "e";
        $target = [
            "lastName" => $lastname,
            "firstName" => $firstname,
            "email" => $email,
            "phone" => $phone,
            "userId" => $userId
        ];
        $signer = new SignerDraft($lastname, $firstname, $email, $phone, $userId);

        $this->assertEquals($target, $signer->jsonSerialize());
    }
}
