<?php

namespace Bow\Payment\IvoryCost\MTNMobileMoney\Collection;

use Bow\Payment\Common\Utils;
use GuzzleHttp\Client as HttpClient;
use Bow\Payment\IvoryCost\MTNMobileMoney\MomoToken;
use Bow\Payment\IvoryCost\MTNMobileMoney\MomoEnvironment;
use Bow\Payment\Exceptions\PaymentRequestException;

class MomoPayment
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
     * MomoPayment constructor
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
     * Request to pay
     *
     * @param array $data
     * @return array
     * @throws PaymentRequestException
     */
    public function requestToPay(array $data): array
    {
        try {
            $referenceId = $data['reference'] ?? Utils::generateUuid();

            $payload = [
                'amount' => (string) $data['amount'],
                'currency' => $data['currency'] ?? 'XOF',
                'externalId' => $referenceId,
                'payer' => [
                    'partyIdType' => 'MSISDN',
                    'partyId' => $this->formatPhone($data['phone']),
                ],
                'payerMessage' => $data['payer_message'] ?? 'Payment',
                'payeeNote' => $data['payee_note'] ?? 'Payment received',
            ];

            $response = $this->http->post('/collection/v1_0/requesttopay', [
                'headers' => [
                    'Authorization' => $this->token->getAuthorizationHeader(),
                    'X-Reference-Id' => $referenceId,
                    'X-Target-Environment' => $this->environment->isSandbox() ? 'sandbox' : 'live',
                    'Ocp-Apim-Subscription-Key' => $this->environment->getSubscriptionKey(),
                    'Content-Type' => 'application/json',
                ],
                'json' => $payload,
            ]);

            return [
                'success' => $response->getStatusCode() === 202,
                'reference_id' => $referenceId,
                'status' => 'PENDING',
                'message' => 'Payment request submitted successfully',
            ];
        } catch (\Exception $e) {
            throw new PaymentRequestException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Format phone number for MTN API
     *
     * @param string $phone
     * @return string
     */
    private function formatPhone(string $phone): string
    {
        // Remove any non-digit characters
        $phone = preg_replace('/\D/', '', $phone);

        // Ensure it starts with country code
        if (!str_starts_with($phone, '225')) {
            $phone = '225' . $phone;
        }

        return $phone;
    }
}

