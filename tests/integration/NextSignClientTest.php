<?php

namespace NextSignPHP\Tests\Integration;

use NextSignPHP\Domain\Model\DTO\Document;
use NextSignPHP\Domain\Model\DTO\SignatureMark;
use NextSignPHP\Domain\Model\DTO\Signer;
use NextSignPHP\Domain\Model\DTO\SignerDraft;
use NextSignPHP\Domain\Model\DTO\TransactionDraft;
use NextSignPHP\Domain\Model\DTO\TransactionDraftAddress;
use NextSignPHP\Domain\Model\DTO\TransactionId;
use NextSignPHP\Domain\Model\DTO\User;
use NextSignPHP\Domain\Model\NextSign\TransactionType;
use NextSignPHP\NextSignClient;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use Symfony\Contracts\HttpClient\Exception\HttpExceptionInterface;

class NextSignClientTest extends TestCase
{
    private const ID = '634d74c96825d';
    private const SECRET = 'sk_example1234';
    private const TEST_API_ROOT = "http://nginx/";

    public function testCreate(): void
    {
        $id = self::ID;
        $secret = self::SECRET;

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
        $id = self::ID;
        $secret = self::SECRET;

        $file       = Document::fromPath("tests/examples/lorem.PDF");
        $user       = new User("Maelle Bellanger", "123456789abcd", "maelle.b@yopmail.com");
        $mark       = new SignatureMark("grigri", 1, 1, 1, 1.1, 1.1);
        $signer     = [new Signer("Olivier", "Armstrong", "o.armstrong@amestris.gov", "01 23 45 67 89", "", [$mark])];

        $client = new NextSignClient($id, $secret);
        $transaction = $client->createTransaction("test", TransactionType::ALL_SIGNERS, $user, $file, $signer);
        $this->assertInstanceOf(TransactionId::class, $transaction);
    }

    public function testCreateTransactionDraft(): void
    {
        $id = self::ID;
        $secret = self::SECRET;

        $file       = Document::fromPath("tests/examples/lorem.PDF");
        $user       = new User("Maelle Bellanger", "123456789abcd", "maelle.b@yopmail.com");
        $id         = self::ID;
        /** @var array<SignerDraft> $signers */
        $signers     = [new SignerDraft("Olivier", "Armstrong", "o.armstrong@amestris.gov", "01 23 45 67 89")];

        $client = new NextSignClient($id, $secret);
        $transaction = $client->createTransactionDraft(
            "test",
            TransactionType::ALL_SIGNERS,
            $user,
            $file,
            $signers
        );
        $this->assertInstanceOf(TransactionDraftAddress::class, $transaction);
    }
    /**
     * fails because of bugs on the api we are connecting to
     */
    public function failingtestGetTransactionDraft(): void
    {
        $id = self::ID;
        $secret = self::SECRET;

        $name = "tester";
        $type = TransactionType::ALL_SIGNERS;
        $document = Document::fromPath("tests/examples/lorem.PDF");
        $user = new User("Maelle Bellanger", "123456789abcd", "maelle.b@yopmail.com");
        $signers = [new SignerDraft("Olivier", "Armstrong", "o.armstrong@amestris.gov", "01 23 45 67 89")];

        $target = new TransactionDraft($name, $type, $id, $document, $user, $signers);

        $client = new NextSignClient($id, $secret);
        $adress = $client->createTransactionDraft($name, $type, $user, $document, $signers);
        $transaction = $client->getTransactionDraft($adress->id->id);

        $this->assertEquals($target, $transaction);
    }
}
