<?php

namespace NextSignPHP\Tests\Integration;

use NextSignPHP\Domain\Model\DTO\Document;
use NextSignPHP\Domain\Model\DTO\SignatureMark;
use NextSignPHP\Domain\Model\DTO\Signer;
use NextSignPHP\Domain\Model\DTO\TransactionId;
use NextSignPHP\Domain\Model\DTO\User;
use NextSignPHP\Domain\Model\NextSign\TransactionType;
use NextSignPHP\NextSignClient;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Contracts\HttpClient\Exception\HttpExceptionInterface;

class NextSignClientTest extends TestCase
{
    private const TEST_API_ROOT = "http://nginx/";

    public function testCreate(): void
    {
        $id = "634d74c96825d";
        $secret = "sk_example1234";

        $client = new NextSignClient($id, $secret, null, self::TEST_API_ROOT);

        $this->assertInstanceOf(NextSignClient::class, $client);
        $reflector = new ReflectionClass(NextSignClient::class);
        $this->assertIsString($reflector->getProperty("token")->getValue($client));
    }

    public function testFailCreateInternalServer(): void
    {
        // TODO, more precise exception, for when it'll be a 4XX instead of a 500
        $this->expectException(HttpExceptionInterface::class);
        $id = "does_not_exist";
        $secret = "does_not_exist";

        new NextSignClient($id, $secret, null, self::TEST_API_ROOT);
    }

    public function testCreateTransaction(): void
    {
        $id = "634d74c96825d";
        $secret = "sk_example1234";

        $file       = new Document("tests/examples/fp.pdf");
        $user       = new User("634d74c96825d", "Maelle Bellanger", "123456789abcd", "maelle.b@yopmail.com");
        $mark       = new SignatureMark("grigri", 1, 1, 1, 1.1, 1.1);
        $signer     = new Signer("Olivier", "Armstrong", "o.armstrong@amestris.gov", "01 23 45 67 89", "", [$mark]);

        $client = new NextSignClient($id, $secret);
        $transaction = $client->createTransaction("test", TransactionType::ALL_SIGNERS, $user, $file, [$signer]);
        $this->assertInstanceOf(TransactionId::class, $transaction);
    }
}
