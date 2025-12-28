<?php

namespace Bow\Payment\Common;

use Bow\Payment\Exceptions\InputValidationException;

interface ProcessorGatewayInterface
{
    /**
     * Make payment
     * 
     * @param array $params
     * @return mixed
     */
    public function payment(array $params);

    /**
     * Make transfer
     * 
     * @param array $params
     * @return mixed
     */
    public function transfer(array $params);

    /**
     * Get balance
     * 
     * @param array $params
     * @return mixed
     */
    public function balance(array $params = []);

    /**
     * Verify payment
     *
     * @param array $params
     * @return void
     */
    public function verify(array $params);

    /**
     * Validate payment data
     * 
     * @param array $params
     * @throws InputValidationException
     * @return void
     */
    public function validatePaymentData(array $params): void;
}
