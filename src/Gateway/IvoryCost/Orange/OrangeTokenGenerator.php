<?php

namespace Bow\Payment\Gateway\IvoryCost\Orange;

use \GuzzleHttp\Client as HttpClient;
use Bow\Payment\Gateway\IvoryCost\Orange\OrangeToken;

class OrangeTokenGenerator
{
    /**
     * HTTP client instance
     *
     * @var HttpClient
     */
    private $http;
    
    /**
     * The get token url
     *
     * @var string
     */
    private $get_token_url;
    
    /**
     * The basic authentication value
     *
     * @var string
     */
    private $key;
    
    /**
     * Orange constructor
     *
     * @param string $key
     * @return mixed
     */
    public function __construct(string $key)
    {
        $this->http = new HttpClient(['base_uri' => 'https://api.orange.com']);
        
        $this->get_token_url = '/oauth/v2/token';
        
        $this->key = $key;
    }

    /**
     * Get payment token
     *
     * @return OrangeToken
     */
    public function getToken()
    {
        $response = $this->http->post($this->get_token_url, [
            'form_params' => ['grant_type' => 'client_credentials'],
            'headers' => ['Authorization' => 'Basic ' . $this->key]
        ]);
        
        // Get the response content
        $content = $response->getBody()->getContents();
        
        $token = json_decode($content);

        return new OrangeToken(
            $token->access_token,
            $token->token_type,
            $token->expires_in
        );
    }

    /**
     * Set the get token url
     *
     * @deprecated
     * @param string $url
     * @return mixed
     */
    public function setUrl(string $url)
    {
        $this->get_token_url = $url;
    }

    /**
     * Set the get token url
     *
     * @param string $url
     * @return mixed
     */
    public function setTokenGeneratorEndpoint(string $url)
    {
        $this->get_token_url = $url;
    }

    /**
     * Set the get token url
     *
     * @param string $key
     * @return mixed
     */
    public function setCredentials($key)
    {
        $this->key = $key;
    }
}
