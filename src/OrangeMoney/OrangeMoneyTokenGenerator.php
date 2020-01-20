<?php

namespace Bow\Payment\OrangeMoney;

use \GuzzleHttp\Client as HttpClient;

class OrangeMoneyTokenGenerator
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
    private $header;
    
    /**
     * OrangeMoney contructor
     *
     * @return mixed
     */
    public function __construct($header)
    {
        $this->http = new HttpClient;
        
        $this->get_token_url = 'https://api.orange.com/oauth/v2/token';
        
        $this->header = $header;
    }
    
    /**
     * Get payment token
     *
     * @return OrangeMoneyToken
     */
    public function getToken()
    {
        $response = $this->http->post($this->get_token_url, [
            'form_paramas' => ['grant_type' => 'client_credentials'],
            'headers' => ['Authorization' => 'Basic ' . $this->header]
        ]);
        
        // Get the response content
        $content = $response->getBody()->getContents();
        
        $token = json_encode($content);
        
        return new OrangeMoneyToken(
            $token->access_token,
            $token->token_type,
            $token->expires_in
        );
    }

    /**
     * Set the get token url
     *
     * @param string $url
     * @return mixed
     */
    public function setUrl($url)
    {
        $this->get_token_url = $url;
    }

    /**
     * Set the get token url
     *
     * @param string $header
     * @return mixed
     */
    public function setHeader($header)
    {
        $this->header = $header;
    }
}
