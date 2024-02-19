<?php

namespace NextSignPHP\Tests\Integration;

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
}
