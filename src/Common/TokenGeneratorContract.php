<?php

namespace Bow\Payment\Common;

use Bow\Payment\Common\PaymentToken;

interface TokenGeneratorContract
{
    /**
     * Get payment token
     *
     * @return PaymentToken
     */
    public function getToken();

    /**
     * Set the get token url
     *
     * @deprecated
     * @param string $url
     * @return mixed
     */
    public function setUrl(string $url);

    /**
     * Set the get token url
     *
     * @param string $url
     * @return mixed
     */
    public function setTokenGeneratorEndpoint(string $url);

    /**
     * Set credentials
     *
     * @param mixed $credentials
     * @return mixed
     */
    public function setCredentials($credentials);
}
