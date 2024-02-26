<?php

namespace features;

use Behat\Behat\Context\Context;
use Behat\Behat\Tester\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;

/**
 * Defines application features from the specific context.
 */
class GetTransactionDraftContext implements Context
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
     * @Given There is an id :arg1
     */
    public function thereIsAnId($arg1)
    {
        throw new PendingException();
    }

    /**
     * @When there is a demand to get the corresponding transaction draft
     */
    public function thereIsADemandToGetTheCorrespondingTransactionDraft()
    {
        throw new PendingException();
    }

    /**
     * @Then the transaction draft is returned
     */
    public function theTransactionDraftIsReturned()
    {
        throw new PendingException();
    }
}
