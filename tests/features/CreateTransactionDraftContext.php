<?php

namespace features;

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use NextSignPHP\Domain\Model\DTO\Document;
use NextSignPHP\Domain\Model\DTO\SignerDraft;
use NextSignPHP\Domain\Model\DTO\TransactionDraftAddress;
use NextSignPHP\Domain\Model\DTO\User;
use NextSignPHP\Domain\Model\NextSign\TransactionType;
use NextSignPHP\NextSignClient;
use PHPUnit\Framework\Assert;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

/**
 * Defines application features from the specific context.
 */
class CreateTransactionDraftContext implements Context
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
     * @Given There is a api, a file :arg1 and a user but no marks
     */
    public function thereIsAApiAFileAndAUserButNoMarks(string $arg1): void
    {

        $mockhttp = new MockHttpClient([new MockResponse('{"token": "example"}'), new MockResponse('{
            "success": true,
            "data": {
              "transactionId": "ns_tra_18c8b76ae6cc5474cccf596c2c",
              "transactionEditorUrl": "https://app.nextsign.fr/prepare-transaction"
            },
            "error_code": 200,
            "message": ""
          }')]);
        $this->client = new NextSignClient("634d74c96825d", "sk_example1234", $mockhttp);
        $this->file = $arg1;
    }

    private TransactionDraftAddress $result;
    /**
     * @When there is a demand to create a transaction draft
     */
    public function thereIsADemandToCreateATransactionDraft(): void
    {
        $file   = Document::fromPath($this->file);
        $user   = new User("Maelle Bellanger", "123456789abcd", "maelle.b@yopmail.com");
        $signer = new SignerDraft("Olivier", "Armstrong", "o.armstrong@amestris.gov", "01 23 45 67 89");

        $this->result = $this->client->createTransactionDraft(
            "test",
            TransactionType::ALL_SIGNERS,
            $user,
            $file,
            [$signer]
        );
    }

    /**
     * @Then the transaction is created and is returned
     */
    public function theTransactionIsCreatedAndIsReturned(): void
    {
        Assert::assertInstanceOf(TransactionDraftAddress::class, $this->result);
    }
}
