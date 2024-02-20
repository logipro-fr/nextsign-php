<?php

namespace features;

use Behat\Behat\Context\Context;
use Behat\Behat\Tester\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;

/**
 * Defines application features from the specific context.
 */
class CreateTransactionContext implements Context
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
     * @Given There is a api, a :file :arg1 and a :user :arg2
     */
    public function thereIsAApiAFileAndAUser(string $arg1, string $arg2): void
    {
        throw new PendingException();
    }

    /**
     * @When there is a demand to create a transaction
     */
    public function thereIsADemandToCreateATransaction(): void
    {
        throw new PendingException();
    }

    /**
     * @Then the transaction is created and its ID is returned
     */
    public function theTransactionIsCreatedAndItsIdIsReturned(): void
    {
        throw new PendingException();
    }
}
