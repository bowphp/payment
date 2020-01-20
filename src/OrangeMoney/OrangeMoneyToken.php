<?php

namespace Bow\Payment\OrangeMoney;

class OrangeMoneyToken
{
    /**
     * The access token value
     *
     * @var string
     */
    private $access_token;
    
    /**
     * The type of token who define how to call the API
     *
     * @var string
     */
    private $token_type;
    
    /**
     * The token expiration time
     *
     * @var int
     */
    private $expires_in;
    
    /**
     * OrangeMoneyToken constructor
     *
     * @param string $access_token
     * @param string $token_type
     * @param string $expires_in
     */
    public function __construct($access_token, $token_type, $expires_in)
    {
        $this->access_token = $access_token;
        
        $this->token_type = $token_type;
        
        $this->expires_in = $expires_in;
    }
    
    /**
     * __toString
     *
     * @var string
     */
    public function __toString()
    {
        return $this->token_type . ' ' . $this->access_token;
    }

    /**
     * Get the access token
     * 
     * @return string
     */
    public function getAccessToken()
    {
        return $this->access_token;
    }

    /**
     * Get the token type
     * 
     * @return string
     */
    public function getType()
    {
        return $this->token_type;
    }

    /**
     * Get the expiration time
     * 
     * @return string
     */
    public function getExpiresIn()
    {
        return $this->expires_in;
    }

    /**
     * Get the expiration time
     * 
     * @return string
     */
    public function hasExpired()
    {
        return $this->expires_in;
    }
}
