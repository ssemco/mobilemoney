<?php

namespace Ssemco\MobileMoney;

use GuzzleHttp\Client;

class MobileMoney
{
    protected $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function accessToken()
    {
        $authorization = 'Basic ' . base64_encode(config("mobilemoney.mtn.user_id") . ':' . config("mobilemoney.mtn.user_key"));

        try {
            $response = $this->client->post('https://ericssonbasicapi2.azure-api.net/collection/token/', [
                'headers' => [
                    'Authorization' => $authorization,
                    'Ocp-Apim-Subscription-Key' => config('mobilemoney.mtn.primary_key')
                ]
            ]);

            return collect(json_decode($response->getBody()->getContents(), true));

        } catch (\Exception $e) {
            throw new \Exception("Could not get access token", 500);
        }

    }

    public function pay()
    {
        $accessToken = $this->accessToken();

        $response = $this->client->post('https://ericssonbasicapi2.azure-api.net/collection/v1_0/requesttopay',
            [
                'headers' => [
                    'X-Reference-Id' => config('mobilemoney.mtn.user_id'),
                    'X-Target-Environment' => 'sandbox',
                    'Ocp-Apim-Subscription-Key' => config('mobilemoney.mtn.primary_key')
                ]
            ]
        );

        return collect($response->getBody()->getContents());
    }
}