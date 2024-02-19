<?php

namespace NextSignPHP\Tests\Integration;

use NextSignPHP\NextSignClient;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class NextSignClientTest extends TestCase
{
    public function testCreate(): void
    {
        $id = "634d74c96825d";
        $secret = "sk_example1234";
        
        $client = new NextSignClient($id, $secret);
        
        $this->assertInstanceOf(NextSignClient::class, $client);
        $reflector = new ReflectionClass(NextSignClient::class);
        $this->assertIsString($reflector->getProperty("token")->getValue($client));
    }

    public function testFailCreateInternalServer(): void
    {
        $this->expectException(UnauthorizedHttpException::class);
        $id = "does_not_exist";
        $secret = "does_not_exist";
        
        new NextSignClient($id, $secret);
    }
}