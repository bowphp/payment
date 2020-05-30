<?php

namespace Bow\Payment\MTMMobileMoney;

use \GuzzleHttp\Client as HttpClient;

class MomoEnvironment
{
    /**
     * HTTP client instance
     *
     * @var HttpClient
     */
    private $http;

    /**
     * The environment system
     *
     * @var MomoEnvironment
     */
    private $environment;

    /**
     * The name of payment action interface
     *
     * @var string
     */
    private $interface_name;

    /**
     * MomoEnvironment constructor
     *
     * @param MomoEnvironment $environment
     * @return mixed
     */
    public function __construct(MomoEnvironment $environment, $interface_name = 'collection')
    {
        $this->environment = $environment;

        $this->http = new HttpClient(['base_uri' => $this->environment->getBaseUri()]);

        $this->interface_name = $interface_name;
    }

    /**
     * Get the token
     *
     * @return string
     */
    public function getToken()
    {
        $headers = $this->environment->getAuthorization();

        $response = $this->http->post('/'.$this->interface_name.'/token', [
            'headers' => $headers
        ]);
        
        // Get the response content
        $content = $response->getBody()->getContents();
        
        $token = json_decode($content);

        return new MomoToken(
            $token->access_token,
            $token->token_type,
            $token->expires_in
        );
    }

    /**
     * Set the interface type nane
     *
     * @param string $name
     */
    public function setInterfaceName($name)
    {
        $this->interface_name = $name;
    }
}
