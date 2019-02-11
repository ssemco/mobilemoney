<?php

namespace Ssemco\MobileMoney;

use GuzzleHttp\Client;
use Illuminate\Support\Str;

class MobileMoney
{
    protected $client;
    protected $paymentRequestOptions = [
        'amount',
        'currency',
        'externalId',
        'payerType',
        'payerId',
        'payerMessage',
        'payeeMessage'
    ];
    protected $missingKeys = [];

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

            $contents = $this->getResponseBody($response);

            $token = $contents->get('access_token');

            return $token;

        } catch (\Exception $e) {
            throw new \Exception("Could not get access token", 500);
        }

    }

    public function request(array $options)
    {
        if (! $this->hasValidKeys($options)) {
            throw new \Exception("Missing required key: " . $this->missingKeys[0], 400);
        }

        $this->referenceId = (string) Str::uuid();
        $this->accessToken = $this->accessToken();
        // Request the payment
        $response = $this->client->post('https://ericssonbasicapi2.azure-api.net/collection/v1_0/requesttopay',
            [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->accessToken,
                    'X-Reference-Id' => $this->referenceId,
                    'X-Target-Environment' => 'sandbox',
                    'Ocp-Apim-Subscription-Key' => config('mobilemoney.mtn.primary_key'),
                ],
                'json' => [
                    'amount' => $options['amount'] ,
                    'currency' => $options['currency'],
                    'externalId' => $options['externalId'],
                    'payer' => [
                        'partyIdType' => $options['payerType'],
                        'partyId' => $options['payerId']
                    ],
                    'payerMessage' => $options['payerMessage'],
                    'payeeNote' => $options['payeeMessage']
                ]
            ]
        );

        return collect([
            'reference_id' => $this->referenceId,
            'access_token' => $this->accessToken
        ]);
    }

    public function requestStatus(string $referenceId)
    {
        try {
            $response = $this->client->get(
                'https://ericssonbasicapi2.azure-api.net/collection/v1_0/requesttopay/' . $referenceId,
                [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $this->accessToken(),
                        'X-Target-Environment' => 'sandbox',
                        'Ocp-Apim-Subscription-Key' => config('mobilemoney.mtn.primary_key'),
                    ]
                ]
            );

            return $this->getResponseBody($response);

        } catch (\Exception $e) {

            return collect(['error' => $e->getMessage()]);
        }

    }

    public function paymentSuccessful(string $referenceId) : bool
    {
        try {
            $payment = $this->requestStatus($referenceId);

            return strtoupper($payment->get('status', 'FAIL')) === 'SUCCESSFUL';

        } catch (\Exception $e) {
            return false;
        }

    }

    protected function missingKeys(array $source) : array {
        $missingKeys = array_diff($this->paymentRequestOptions, array_keys($source));

        $this->missingKeys = array_values($missingKeys);

        return $this->missingKeys;
    }

    protected function hasValidKeys(array $source)
    {
        if (count($this->missingKeys($source)) === 0) {
            return true;
        }

        return false;
    }

    protected function getResponseBody($response)
    {
        return collect(json_decode($response->getBody()->getContents(), true));
    }
}