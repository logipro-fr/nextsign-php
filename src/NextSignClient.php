<?php

namespace NextSignPHP;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

use function Safe\json_decode;

class NextSignClient
{
    private string $token;
    private HttpClientInterface $client;

    public function __construct(
        string $client_id,
        string $client_secret,
        ?HttpClientInterface $httpClient = null
    )
    {
        if($httpClient === null){
            $this->client = HttpClient::create([
                'headers' => [
                    'Content-Type' => 'application/json',
                ]
            ]);
        }
        else{
            $this->client = $httpClient;
        }

        $response = $this->client->request(
            "POST",
            "http://localhost:33080/v1/token",
            [
                "body" => [
                    "client_id" => $client_id, 
                    "client_secret" => $client_secret
                ]
            ]
        );
        /** @var object{token: string} $date */
        $data = json_decode($response->getContent());
        $this->token = $data->token;
    }
}