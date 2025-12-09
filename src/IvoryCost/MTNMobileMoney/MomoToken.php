<?php

namespace Bow\Payment\IvoryCost\MTNMobileMoney;

class MomoToken
{
    /**
     * Create a new MomoToken instance
     *
     * @param string $accessToken
     * @param string $tokenType
     * @param int $expiresIn
     */
    public function __construct(
        private string $accessToken,
        private string $tokenType,
        private int $expiresIn
    ) {
    }

    /**
     * Get the access token
     *
     * @return string
     */
    public function getAccessToken(): string
    {
        return $this->accessToken;
    }

    /**
     * Get the token type
     *
     * @return string
     */
    public function getTokenType(): string
    {
        return $this->tokenType;
    }

    /**
     * Get expiration time in seconds
     *
     * @return int
     */
    public function getExpiresIn(): int
    {
        return $this->expiresIn;
    }

    /**
     * Get the full authorization header value
     *
     * @return string
     */
    public function getAuthorizationHeader(): string
    {
        return "{$this->tokenType} {$this->accessToken}";
    }
}
