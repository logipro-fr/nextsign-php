<?php

namespace NextSignPHP\Tests;

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
use Symfony\Component\HttpClient\Exception\InvalidArgumentException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

use function Safe\json_encode;

class NextSignClientTest extends TestCase
{
    private const ID = '634d74c96825d';
    private const SECRET = 'sk_example1234';
    private const LOGIN_RESPONSE = '{"token": "example"}';

    public function testCreate(): void
    {
        $id = self::ID;
        $secret = self::SECRET;

        $mockhttp = $this->createMock(HttpClientInterface::class);
        $mockResponse = $this->createMock(ResponseInterface::class);
        $mockResponse->method("getContent")->willReturn(self::LOGIN_RESPONSE);
        $mockhttp->method("request")->willReturn($mockResponse);

        $client = new NextSignClient($id, $secret, $mockhttp);

        $this->assertInstanceOf(NextSignClient::class, $client);
        $reflector = new ReflectionClass(NextSignClient::class);
        $this->assertEquals("example", $reflector->getProperty("token")->getValue($client));
    }
    public function testFailCreate(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid URL: scheme is missing in "/v1/token".' .
            ' Did you forget to add "http(s)://"?');
        $id = "";
        $secret = "";


        new NextSignClient($id, $secret, null, "");
    }

    public function testCreateCustomUrl(): void
    {
        $id = self::ID;
        $secret = self::SECRET;

        $mockhttp = $this->createMock(HttpClientInterface::class);
        $mockResponse = $this->createMock(ResponseInterface::class);
        $mockResponse->method("getContent")->willReturn(self::LOGIN_RESPONSE);
        $mockhttp->method("request")->willReturn($mockResponse);

        $client = new NextSignClient($id, $secret, $mockhttp, "http://example.com");

        $this->assertInstanceOf(NextSignClient::class, $client);
        $reflector = new ReflectionClass(NextSignClient::class);
        $this->assertEquals("example", $reflector->getProperty("token")->getValue($client));
        $this->assertEquals("http://example.com/", $reflector->getProperty("baseApiUrl")->getValue($client));
    }

    public function testFailCreateInternalServer(): void
    {
        $this->expectException(UnauthorizedHttpException::class);
        $id = "does_not_exist";
        $secret = "does_not_exist";
        $mockhttp = $this->createMock(HttpClientInterface::class);

        $mockResponse = $this->createMock(ResponseInterface::class);
        $mockResponse->method("getContent")->willReturn('{"garbage": "data"}');
        $mockResponse->method("getContent")
            ->willThrowException(new UnauthorizedHttpException('Basic realm="access to the API'));
        $mockResponse->method("getStatusCode")->willReturn(401);
        $mockhttp->method("request")->willReturn($mockResponse);

        new NextSignClient($id, $secret, $mockhttp);
    }

    public function testCreateTransaction(): void
    {
        $id = self::ID;
        $secret = self::SECRET;

        $mockhttp = $this->createMock(HttpClientInterface::class);
        $mockResponse = $this->createMock(ResponseInterface::class);
        $mockResponse->method("getContent")->willReturn(self::LOGIN_RESPONSE, '{
            "success": true,
            "data": {
                "transactionId": "ns_tra_18c8b76ae6cc5474cccf596c2c",
                "numberOfSignaturesUsed": 1
            },
            "error_code": 200,
            "message": ""
            }');

        $file       = Document::fromPath("tests/examples/lorem.PDF");
        $user       = new User("Maelle Bellanger", "123456789abcd", "maelle.b@yopmail.com");
        $mark       = new SignatureMark("grigri", 1, 1, 1, 1, 1);
        $signers    = [new Signer("Olivier", "Armstrong", "o.armstrong@amestris.gov", "01 23 45 67 89", "", [$mark])];

        $map = [
            [
                "POST",
                "http://example.com/v1/transaction",
                [
                    "body" => json_encode([
                        "transactionName" => "test",
                        "strategy" => TransactionType::ALL_SIGNERS,
                        "document" => $file,
                        "accountId" => $id,
                        "contractorName" => $user->name,
                        "contractorUserId" => $user->userId,
                        "contractorEmail" => $user->email,
                        "signers" => $signers
                    ]),
                    "headers" => [
                        "Authorization" => "Bearer example"
                    ]
                    ],
                    $mockResponse
            ],
            $this->createTokenMap($id, $secret, $mockResponse)
        ];
        $mockhttp->method("request")->willReturnMap($map);

        $client = new NextSignClient($id, $secret, $mockhttp, "http://example.com");

        $transaction = $client->createTransaction("test", TransactionType::ALL_SIGNERS, $user, $file, $signers);
        $this->assertEquals(new TransactionId("ns_tra_18c8b76ae6cc5474cccf596c2c"), $transaction);
    }

    public function testCreateTransactionDraft(): void
    {
        $id = self::ID;
        $secret = self::SECRET;

        $mockhttp = $this->createMock(HttpClientInterface::class);
        $mockResponse = $this->createMock(ResponseInterface::class);
        $id = "ns_tra_18c8b76ae6cc5474cccf596c2c";
        $url = "https://app.nextsign.fr/prepare-transaction";
        $mockResponse->method("getContent")->willReturn(self::LOGIN_RESPONSE, '{
            "success": true,
            "data": {
                "transactionDraftId": "' . $id . '",
                "transactionEditorUrl": "' . $url . '"
            },
            "error_code": 200,
            "message": ""
            }');

        $file       = Document::fromPath("tests/examples/lorem.PDF");
        $user       = new User("Maelle Bellanger", "123456789abcd", "maelle.b@yopmail.com");
        $signers     = [new SignerDraft("Olivier", "Armstrong", "o.armstrong@amestris.gov", "01 23 45 67 89")];

        $map = [
            [
                "POST",
                "http://example.com/v1/transaction-draft",
                [
                    "body" => json_encode([
                        "transactionName" => "test",
                        "strategy" => TransactionType::ALL_SIGNERS,
                        "document" => $file,
                        "accountId" => $id,
                        "contractor" => [
                            "name" => $user->name,
                            "userId" => $user->userId,
                            "email" => $user->email
                        ],
                        "signers" => $signers
                    ]),
                    "headers" => [
                        "Authorization" => "Bearer example"
                    ]
                    ],
                    $mockResponse
            ],
            $this->createTokenMap($id, $secret, $mockResponse)
        ];
        $mockhttp->method("request")->willReturnMap($map);

        $target = new TransactionDraftAddress(new TransactionId($id), $url);

        $client = new NextSignClient($id, $secret, $mockhttp, "http://example.com");


        $transaction = $client->createTransactionDraft(
            "test",
            TransactionType::ALL_SIGNERS,
            $user,
            $file,
            $signers
        );
        $this->assertEquals($target, $transaction);
    }

    public function testGetTransactionDraft(): void
    {
        $name = "test";
        $type = TransactionType::ALL_SIGNERS;
        $document = Document::fromPath("tests/examples/lorem.PDF");
        $user = new User("Maelle Bellanger", "123456789abcd", "maelle.b@yopmail.com");
        $id = self::ID;
        $signers = [
            "lastname" => "Armstrong",
            "firstname" => "Olivier",
            "email" => "o.armstrong@amestris.gov",
            "phone" => "01 23 45 67 89"
        ];
        $data = [
            "transactionName" => $name,
            "strategy" => $type,
            "accountId" => $id,
            "document" => $document,
            "contractor" => [
                "fullName" => $user->name,
                "userId" => $user->userId,
                "email" => $user->email
            ],
            "signers" => [$signers]
        ];
        $result = json_encode($data);

        $target = new TransactionDraft(
            $name,
            $type,
            $id,
            $document,
            $user,
            [new SignerDraft("Olivier", "Armstrong", "o.armstrong@amestris.gov", "01 23 45 67 89")]
        );

        $mockhttp = $this->createMock(HttpClientInterface::class);
        $mockResponse = $this->createMock(ResponseInterface::class);
        $id = "ns_tra_18c8b76ae6cc5474cccf596c2c";
        $mockResponse->method("getContent")->willReturn(self::LOGIN_RESPONSE, $result);
        //$mockhttp->method("request")->willReturn($mockResponse);


        $map = [
            [
                "GET",
                "http://example.com/v1/transaction-draft",
                [
                    "query" => [
                        "transactionDraftId" => "test"
                    ],
                    "headers" => [
                        "Authorization" => "Bearer example",
                        "accept" => "application/json"
                    ]
                ],
                $mockResponse
            ],
            $this->createTokenMap("", "", $mockResponse)
        ];
        $mockhttp->method("request")->willReturnMap($map);

        $client = new NextSignClient("", "", $mockhttp, "http://example.com");
        $transaction = $client->getTransactionDraft("test");

        $this->assertEquals($target, $transaction);
    }

    /**
     * @return array<mixed>
     */
    private function createTokenMap(string $id, string $secret, ResponseInterface $response): array
    {
        return [
            "POST",
            "http://example.com/v1/token",
            [
                "body" => json_encode([
                    "client_id" => $id,
                    "client_secret" => $secret
                ]),
            ],
            $response
        ];
    }
}
