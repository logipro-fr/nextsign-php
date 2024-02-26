<?php

namespace NextSignPHP;

use NextSignPHP\Domain\Model\DTO\Document;
use NextSignPHP\Domain\Model\DTO\Signer;
use NextSignPHP\Domain\Model\DTO\SignerDraft;
use NextSignPHP\Domain\Model\DTO\TransactionDraft;
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
    private const CREATE_TRANSACTION_DRAFT_URI = "v1/transaction-draft";
    private const DEFAULT_CLIENT_HEADERS = [
        'Content-Type' => 'application/json'
    ];

    private string $token;
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
        $this->token = $this->requestToken($client_id, $client_secret);
        if (!str_ends_with($this->baseApiUrl, "/")) {
            $this->baseApiUrl .= "/";
        }
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
                    "accountId" => $user->accountId,
                    "contractorName" => $user->contractorName,
                    "contractorUserId" => $user->contractorUserId,
                    "contractorEmail" => $user->contractorEmail,
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
    ): TransactionDraft {
        $response = $this->client->request(
            "POST",
            $this->baseApiUrl . self::CREATE_TRANSACTION_DRAFT_URI,
            [
                "body" => json_encode([
                    "transactionName" => $name,
                    "strategy" => $type,
                    "document" => $document,
                    "accountId" => $user->accountId,
                    "contractor" => [
                        "name" => $user->contractorName,
                        "userId" => $user->contractorUserId,
                        "email" => $user->contractorEmail
                    ],
                    "signers" => $signers
                ]),
                "headers" => [
                    "Authorization" => "Bearer " . $this->token
                ]
            ]
        );
        /** @var object{data: object{transactionId: string, transactionEditorUrl: string}} $data */
        $data = json_decode($response->getContent());
        return new TransactionDraft(
            new TransactionId($data->data->transactionId),
            $data->data->transactionEditorUrl
        );
    }
}
