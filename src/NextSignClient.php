<?php

namespace NextSignPHP;

use NextSignPHP\Domain\Model\DTO\Document;
use NextSignPHP\Domain\Model\DTO\Signer;
use NextSignPHP\Domain\Model\DTO\SignerDraft;
use NextSignPHP\Domain\Model\DTO\TransactionDraft;
use NextSignPHP\Domain\Model\DTO\TransactionDraftAddress;
use NextSignPHP\Domain\Model\DTO\TransactionId;
use NextSignPHP\Domain\Model\DTO\User;
use NextSignPHP\Domain\Model\NextSign\TransactionType;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpClient\HttpOptions;
use Symfony\Contracts\HttpClient\HttpClientInterface;

use function Safe\json_decode;
use function Safe\json_encode;

class NextSignClient
{
    private const TOKEN_URI = "v1/token";
    private const CREATE_TRANSACTION_URI = "v1/transaction";
    private const TRANSACTION_DRAFT_URI = "v1/transaction-draft";
    private const DEFAULT_CLIENT_HEADERS = [
        'Content-Type' => 'application/json'
    ];

    private string $token;
    private string $id;
    private HttpClientInterface $client;

    public function __construct(
        string $client_id,
        string $client_secret,
        ?HttpClientInterface $httpClient = null,
        private string $baseApiUrl = "http://nginx/" //TODO add production uri
    ) {
        if ($httpClient === null) {
            $this->client = HttpClient::create()->withOptions((new HttpOptions())
                ->setHeaders(self::DEFAULT_CLIENT_HEADERS)
                ->toArray());
        } else {
            $this->client = $httpClient;
        }
        if (!str_ends_with($this->baseApiUrl, "/")) {
            $this->baseApiUrl .= "/";
        }
        $this->id = $client_id;
        $this->token = $this->requestToken($client_id, $client_secret);
    }

    private function requestToken(string $client_id, string $client_secret): string
    {
        $response = $this->client->request(
            "POST",
            $this->baseApiUrl . self::TOKEN_URI,
            [
                "body" => json_encode([
                    "client_id" => $client_id,
                    "client_secret" => $client_secret
                ]),
            ]
        );
        /** @var object{token: string} $data */
        $data = json_decode($response->getContent());
        return $data->token;
    }

    /**
     * @param array<Signer> $signers
     */
    public function createTransaction(
        string $name,
        TransactionType $type,
        User $user,
        Document $document,
        array $signers
    ): TransactionId {
        $response = $this->client->request(
            "POST",
            $this->baseApiUrl . self::CREATE_TRANSACTION_URI,
            [
                "body" => json_encode([
                    "transactionName" => $name,
                    "strategy" => $type,
                    "document" => $document,
                    "accountId" => $this->id,
                    "contractorName" => $user->name,
                    "contractorUserId" => $user->userId,
                    "contractorEmail" => $user->email,
                    "signers" => $signers
                ]),
                "headers" => [
                    "Authorization" => "Bearer " . $this->token
                ]
            ]
        );
        /** @var object{data: object{transactionId: string}} $data */
        $data = json_decode($response->getContent());
        return new TransactionId($data->data->transactionId);
    }

    /**
     * @param array<SignerDraft> $signers
     */
    public function createTransactionDraft(
        string $name,
        TransactionType $type,
        User $user,
        Document $document,
        array $signers
    ): TransactionDraftAddress {
        $response = $this->client->request(
            "POST",
            $this->baseApiUrl . self::TRANSACTION_DRAFT_URI,
            [
                "body" => json_encode([
                    "transactionName" => $name,
                    "strategy" => $type,
                    "document" => $document,
                    "accountId" => $this->id,
                    "contractor" => [
                        "name" => $user->name,
                        "userId" => $user->userId,
                        "email" => $user->email
                    ],
                    "signers" => $signers
                ]),
                "headers" => [
                    "Authorization" => "Bearer " . $this->token
                ]
            ]
        );
        /** @var object{data: object{transactionDraftId: string, transactionEditorUrl: string}} $data */
        $data = json_decode($response->getContent());
        return new TransactionDraftAddress(
            new TransactionId($data->data->transactionDraftId),
            $data->data->transactionEditorUrl
        );
    }

    public function getTransactionDraft(string $transactionDraftId): TransactionDraft
    {

        $response = $this->client->request(
            "GET",
            $this->baseApiUrl . self::TRANSACTION_DRAFT_URI,
            [
                "query" => [
                    "transactionDraftId" => $transactionDraftId
                ],
                "headers" => [
                    "Authorization" => "Bearer " . $this->token,
                    "accept" => "application/json"
                ]
            ]
        );
        /** @var object{
         *          transactionName: string,
         *          strategy: string,
         *          accountId: string,
         *          document: object{
         *              type: string,
         *              name: string,
         *              content: string
         *          },
         *          contractor: object{
         *              userId: string,
         *              fullName: string,
         *              email: string
         *          },
         *          signers: array<object{
         *              firstname: string,
         *              lastname: string,
         *              phone: string,
         *              email: string
         *          }>
         *  } $data
         */
        $data = json_decode($response->getContent());
        $user = new User(
            $data->contractor->fullName,
            $data->contractor->userId,
            $data->contractor->email
        );
        $document = new Document(
            $data->document->type,
            $data->document->content,
            $data->document->name
        );
        $signers = [];
        foreach ($data->signers as $signer) {
            $sign = new SignerDraft($signer->firstname, $signer->lastname, $signer->email, $signer->phone);
            array_push($signers, $sign);
        }
        return new TransactionDraft(
            $data->transactionName,
            TransactionType::from($data->strategy),
            $data->accountId,
            $document,
            $user,
            $signers
        );
    }
}
