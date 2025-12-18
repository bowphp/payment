<?php

namespace Bow\Payment\Gateway\IvoryCost\MTNMobileMoney\Collection;

use GuzzleHttp\Client as HttpClient;
use Bow\Payment\Gateway\IvoryCost\MTNMobileMoney\MomoToken;
use Bow\Payment\Gateway\IvoryCost\MTNMobileMoney\MomoEnvironment;
use Bow\Payment\Exceptions\TransactionVerificationException;

class MomoTransaction
{
    /**
     * HTTP client instance
     *
     * @var HttpClient
     */
    private $http;

    /**
     * Environment instance
     *
     * @var MomoEnvironment
     */
    private $environment;

    /**
     * Authentication token
     *
     * @var MomoToken
     */
    private $token;

    /**
     * MomoTransaction constructor
     *
     * @param MomoToken $token
     * @param MomoEnvironment $environment
     */
    public function __construct(MomoToken $token, MomoEnvironment $environment)
    {
        $this->token = $token;
        $this->environment = $environment;
        $this->http = new HttpClient(['base_uri' => $this->environment->getBaseUri()]);
    }

    /**
     * Get transaction status
     *
     * @param string $referenceId
     * @return MomoPaymentStatus
     * @throws TransactionVerificationException
     */
    public function getTransactionStatus(string $referenceId): MomoPaymentStatus
    {
        try {
            $response = $this->http->get("/collection/v1_0/requesttopay/{$referenceId}", [
                'headers' => [
                    'Authorization' => $this->token->getAuthorizationHeader(),
                    'X-Target-Environment' => $this->environment->isSandbox() ? 'sandbox' : 'live',
                    'Ocp-Apim-Subscription-Key' => $this->environment->getSubscriptionKey(),
                ],
            ]);

            $data = json_decode($response->getBody()->getContents(), true);

            return new MomoPaymentStatus($data['status'], $data);
        } catch (\Exception $e) {
            throw new TransactionVerificationException($referenceId, $e);
        }
    }

    /**
     * Get account balance
     *
     * @return array
     */
    public function getAccountBalance(): array
    {
        try {
            $response = $this->http->get('/collection/v1_0/account/balance', [
                'headers' => [
                    'Authorization' => $this->token->getAuthorizationHeader(),
                    'X-Target-Environment' => $this->environment->isSandbox() ? 'sandbox' : 'live',
                    'Ocp-Apim-Subscription-Key' => $this->environment->getSubscriptionKey(),
                ],
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (\Exception $e) {
            return [
                'error' => true,
                'message' => $e->getMessage(),
            ];
        }
    }
}
