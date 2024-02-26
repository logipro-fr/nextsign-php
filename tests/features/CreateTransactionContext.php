<?php

namespace features;

use Behat\Behat\Context\Context;
use Behat\Behat\Tester\Exception\PendingException;
use NextSignPHP\Domain\Model\DTO\Document;
use NextSignPHP\Domain\Model\DTO\SignatureMark;
use NextSignPHP\Domain\Model\DTO\Signer;
use NextSignPHP\Domain\Model\DTO\TransactionId;
use NextSignPHP\Domain\Model\DTO\User;
use NextSignPHP\Domain\Model\NextSign\TransactionType;
use NextSignPHP\NextSignClient;
use PHPUnit\Framework\Assert;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

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

    private NextSignClient $client;
    private string $file;

    /**
     * @Given There is a api, a file :arg1 and a user
     */
    public function thereIsAApiAFileAndAUser(string $arg1): void
    {
        $mockhttp = new MockHttpClient([new MockResponse('{"token": "example"}'), new MockResponse('{
            "success": true,
            "data": {
              "transactionId": "ns_tra_18c8b76ae6cc5474cccf596c2c",
              "numberOfSignaturesUsed": 1
            },
            "error_code": 200,
            "message": ""
          }')]);
        $this->client = new NextSignClient("634d74c96825d", "sk_example1234", $mockhttp);
        $this->file = $arg1;
    }

    private TransactionId $result;
    /**
     * @When there is a demand to create a transaction
     */
    public function thereIsADemandToCreateATransaction(): void
    {
        $file       = Document::fromPath($this->file);
        $user       = new User("Maelle Bellanger", "123456789abcd", "maelle.b@yopmail.com");
        $id         = "634d74c96825d";
        $mark       = new SignatureMark("grigri", 1, 1, 1, 1, 1);
        $signer     = new Signer("Olivier", "Armstrong", "o.armstrong@amestris.gov", "01 23 45 67 89", "", [$mark]);

        $this->result = $this->client->createTransaction(
            "test", 
            TransactionType::ALL_SIGNERS, 
            $id, 
            $user, 
            $file, 
            [$signer]
        );
    }

    /**
     * @Then the transaction is created and its ID is returned
     */
    public function theTransactionIsCreatedAndItsIdIsReturned(): void
    {
        Assert::assertInstanceOf(TransactionId::class, $this->result);
    }
}
