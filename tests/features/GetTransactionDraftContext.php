<?php

namespace features;

use Behat\Behat\Context\Context;
use NextSignPHP\Domain\Model\DTO\Document;
use NextSignPHP\Domain\Model\DTO\TransactionDraft;
use NextSignPHP\Domain\Model\DTO\User;
use NextSignPHP\Domain\Model\NextSign\TransactionType;
use NextSignPHP\NextSignClient;
use PHPUnit\Framework\Assert;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

use function Safe\json_encode;

/**
 * Defines application features from the specific context.
 */
class GetTransactionDraftContext implements Context
{
    private NextSignClient $client;
    /**
     * Initializes context.
     *
     * Every scenario gets its own context instance.
     * You can also pass arbitrary arguments to the
     * context constructor through behat.yml.
     */
    public function __construct()
    {
        $name = "test";
        $type = TransactionType::ALL_SIGNERS;
        $document = Document::fromPath("tests/examples/lorem.PDF");
        $user = new User("Maelle Bellanger", "123456789abcd", "maelle.b@yopmail.com");
        $id = "634d74c96825d";
        $signers = [
            "lastname" => "Armstrong",
            "firstname" => "Olivier",
            "email" => "o.armstrong@amestris.gov",
            "phone" => "01 23 45 67 89"
        ];
        $data = [
            "transactionName" => $name,
            "strategy" => $type,
            "accountId" => $id,
            "document" => $document,
            "contractor" => [
                "fullName" => $user->name,
                "userId" => $user->userId,
                "email" => $user->email
            ],
            "signers" => [$signers]
        ];
        $mockhttp = new MockHttpClient([
            new MockResponse('{"token": "example"}'),
            new MockResponse(json_encode($data))
        ]);
        $this->client = new NextSignClient("634d74c96825d", "sk_example1234", $mockhttp);
    }

    private string $id;
    /**
     * @Given There is an id :arg1
     */
    public function thereIsAnId(string $arg1): void
    {
        $this->id = $arg1;
    }

    private TransactionDraft $transaction;
    /**
     * @When there is a demand to get the corresponding transaction draft
     */
    public function thereIsADemandToGetTheCorrespondingTransactionDraft(): void
    {
        $this->transaction = $this->client->getTransactionDraft($this->id);
    }

    /**
     * @Then the transaction draft is returned
     */
    public function theTransactionDraftIsReturned(): void
    {
        Assert::assertInstanceOf(TransactionDraft::class, $this->transaction);
    }
}
