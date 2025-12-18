<?php

namespace Bow\Payment\Gateway\IvoryCost\MTNMobileMoney;

use GuzzleHttp\Client as HttpClient;
use Bow\Payment\Gateway\IvoryCost\MTNMobileMoney\MomoToken;
use Bow\Payment\Gateway\IvoryCost\MTNMobileMoney\MomoEnvironment;
use Bow\Payment\Exceptions\TokenGenerationException;

class MomoTokenGenerator
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
     * Interface name (collection, disbursement, remittance)
     *
     * @var string
     */
    private $interface = 'collection';

    /**
     * MomoTokenGenerator constructor
     *
     * @param MomoEnvironment $environment
     * @param string $interface
     */
    public function __construct(MomoEnvironment $environment, string $interface = 'collection')
    {
        $this->environment = $environment;
        $this->interface = $interface;
        $this->http = new HttpClient(['base_uri' => $this->environment->getBaseUri()]);
    }

    /**
     * Get authentication token
     *
     * @return MomoToken
     * @throws TokenGenerationException
     */
    public function getToken(): MomoToken
    {
        try {
            $response = $this->http->post("/{$this->interface}/token/", [
                'headers' => $this->environment->getAuthorization()
            ]);

            $content = $response->getBody()->getContents();
            $data = json_decode($content, true);

            return new MomoToken(
                $data['access_token'],
                $data['token_type'],
                $data['expires_in']
            );
        } catch (\Exception $e) {
            throw new TokenGenerationException('MTN Mobile Money', $e);
        }
    }

    /**
     * Set the interface name
     *
     * @param string $interface
     * @return void
     */
    public function setInterface(string $interface): void
    {
        $this->interface = $interface;
    }
}
