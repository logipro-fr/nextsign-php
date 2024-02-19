<?php

namespace NextSignPHP\Tests;

use NextSignPHP\NextSignClient;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use Symfony\Component\HttpFoundation\Response;
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
        $mockResponse->method("getStatusCode")->willReturn(200);
        $mockhttp->method("request")->willReturn($mockResponse);
        
        $client = new NextSignClient($id, $secret, $mockhttp);
        
        $this->assertInstanceOf(NextSignClient::class, $client);
        $reflector = new ReflectionClass(NextSignClient::class);
        $this->assertEquals("example",$reflector->getProperty("token")->getValue($client));
    }
}