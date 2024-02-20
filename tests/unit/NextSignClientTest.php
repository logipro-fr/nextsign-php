<?php

namespace NextSignPHP\Tests;

use NextSignPHP\Domain\Model\DTO\Document;
use NextSignPHP\Domain\Model\DTO\SignatureMark;
use NextSignPHP\Domain\Model\DTO\Signer;
use NextSignPHP\Domain\Model\DTO\TransactionId;
use NextSignPHP\Domain\Model\DTO\User;
use NextSignPHP\Domain\Model\NextSign\TransactionType;
use NextSignPHP\NextSignClient;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class NextSignClientTest extends TestCase
{
    public function testCreate(): void
    {
        $id = "634d74c96825d";
        $secret = "sk_example1234";

        $mockhttp = $this->createMock(HttpClientInterface::class);
        $mockResponse = $this->createMock(ResponseInterface::class);
        $mockResponse->method("getContent")->willReturn('{"token": "example"}');
        $mockhttp->method("request")->willReturn($mockResponse);

        $client = new NextSignClient($id, $secret, $mockhttp);

        $this->assertInstanceOf(NextSignClient::class, $client);
        $reflector = new ReflectionClass(NextSignClient::class);
        $this->assertEquals("example", $reflector->getProperty("token")->getValue($client));
    }

    public function testCreateCustomUrl(): void
    {
        $id = "634d74c96825d";
        $secret = "sk_example1234";

        $mockhttp = $this->createMock(HttpClientInterface::class);
        $mockResponse = $this->createMock(ResponseInterface::class);
        $mockResponse->method("getContent")->willReturn('{"token": "example"}');
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

    public function testCreateTransaction(){
        $id = "634d74c96825d";
        $secret = "sk_example1234";

        $mockhttp = $this->createMock(HttpClientInterface::class);
        $mockResponse = $this->createMock(ResponseInterface::class);
        $mockResponse->method("getContent")->willReturn('{"token": "example"}','{
            "success": true,
            "data": {
              "transactionId": "ns_tra_18c8b76ae6cc5474cccf596c2c",
              "numberOfSignaturesUsed": 1
            },
            "error_code": 200,
            "message": ""
          }');
        $mockhttp->method("request")->willReturn($mockResponse);

        $client = new NextSignClient($id, $secret, $mockhttp, "http://example.com");

        $file       = new Document("tests/examples/fp.pdf");
        $user       = new User("634d74c96825d", "Maelle Bellanger", "123456789abcd", "maelle.b@yopmail.com");
        $mark       = new SignatureMark("grigri", 1, 1, 1, 1, 1);
        $signer     = new Signer("Olivier", "Armstrong", "o.armstrong@amestris.gov", "01 23 45 67 89", "", [$mark]);

        $transaction = $client->createTransaction("test", TransactionType::ALL_SIGNERS, $user, $file, [$signer]);
        $this->assertEquals(new TransactionId("ns_tra_18c8b76ae6cc5474cccf596c2c"), $transaction);
    }
}
