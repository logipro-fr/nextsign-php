<?php

namespace NextSignPHP;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpClient\HttpOptions;
use Symfony\Contracts\HttpClient\HttpClientInterface;

use function Safe\json_decode;
use function Safe\json_encode;

class NextSignClient
{
    private const TOKEN_URI = "http://nginx/v1/token";
    private const DEFAULT_CLIENT_HEADERS = [
        'Content-Type' => 'application/json'
    ];

    private string $token;
    private HttpClientInterface $client;

    public function __construct(
        string $client_id,
        string $client_secret,
        ?HttpClientInterface $httpClient = null
    )
    {
        if($httpClient === null){
            $this->client = HttpClient::create()->withOptions((new HttpOptions())
                ->setHeaders(self::DEFAULT_CLIENT_HEADERS)
                ->toArray()
            );
        }
        else{
            $this->client = $httpClient;
        }
        $this->token = $this->requestToken($client_id, $client_secret);
    }

    private function requestToken(string $client_id, string $client_secret){
        $response = $this->client->request(
            "POST",
            self::TOKEN_URI,
            [
                "body" => json_encode([
                    "client_id" => $client_id, 
                    "client_secret" => $client_secret
                ]),
            ]
        );
        /** @var object{token: string} $date */
        $data = json_decode($response->getContent());
        return $data->token;
    }
}