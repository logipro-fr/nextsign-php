<?php

namespace features;

use Behat\Behat\Context\Context;
use Behat\Behat\Tester\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use NextSignPHP\NextSignClient;
use PHPUnit\Framework\Assert;
use ReflectionClass;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

/**
 * Defines application features from the specific context.
 */
class InstanciateContext implements Context
{
    private string $id;
    private string $secret;
    private NextSignClient $cli;
    /**
     * Initializes context.
     *
     * Every scenario gets its own context instance.
     * You can also pass arbitrary arguments to the
     * context constructor through behat.yml.
     */
    public function __construct()
    {
    }
    /**
     * @Given There is a client_id :arg1 and client_secret :arg2
     */
    public function thereIsAClientIdAndClientSecret(string $arg1, string $arg2): void
    {
        $this->id = $arg1;
        $this->secret = $arg2;
    }

    /**
     * @When there is a demand to instantiate the Client with these credentials
     */
    public function thereIsADemandToInstantiateTheClientWithTheseCredentials(): void
    {
        $mockhttp = new MockHttpClient([new MockResponse('{"token": "example"}', [])]);
        $this->cli = new NextSignClient($this->id, $this->secret, $mockhttp);
    }

    /**
     * @Then the Client is instanciated correctly
     */
    public function theClientIsInstanciatedCorrectly(): void
    {
        Assert::assertInstanceOf(NextSignClient::class, $this->cli);
        $reflector = new ReflectionClass(NextSignClient::class);
        Assert::assertEquals("example", $reflector->getProperty("token")->getValue($this->cli));
    }
}
