<?php

namespace NextSignPHP\Tests\Domain\Model\DTO;

use NextSignPHP\Domain\Model\DTO\Signer;
use PHPUnit\Framework\TestCase;

class SignerTest extends TestCase
{
    public function testSerialize(): void
    {
        $lastname = "a";
        $firstname = "b";
        $email = "c";
        $phone = "d";
        $userId = "e";
        $marks = [];
        $target = [
            "lastname" => $lastname,
            "firstname" => $firstname,
            "email" => $email,
            "phone" => $phone,
            "userId" => $userId,
            "signature" => [ "marks" => $marks]
        ];
        $signer = new Signer($lastname, $firstname, $email, $phone, $userId, $marks);

        $this->assertEquals($target, $signer->jsonSerialize());
    }
}
