<?php

namespace features;

use Behat\Behat\Context\Context;
use Behat\Behat\Tester\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;

/**
 * Defines application features from the specific context.
 */
class InstanciateContext implements Context
{
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
    public function thereIsAClientIdAndClientSecret($arg1, $arg2)
    {
        throw new PendingException();
    }

    /**
     * @When there is a demand to instantiate the Client with these credentials
     */
    public function thereIsADemandToInstantiateTheClientWithTheseCredentials()
    {
        throw new PendingException();
    }

    /**
     * @Then the Client is instanciated correctly
     */
    public function theClientIsInstanciatedCorrectly()
    {
        throw new PendingException();
    }
}
