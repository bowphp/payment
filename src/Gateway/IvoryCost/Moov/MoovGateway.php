<?php

namespace Bow\Payment\Gateway\IvoryCost\Moov;

use Bow\Payment\Common\ProcessorGatewayInterface;
use Bow\Payment\Exceptions\PaymentRequestException;

/**
 * Moov Money (Flooz) Gateway
 * Note: This is a placeholder implementation pending official Moov Money API documentation
 */
class MoovGateway implements ProcessorGatewayInterface
{
    /**
     * Configuration array
     *
     * @var array
     */
    private $config;

    /**
     * Create a new Moov Flooz gateway
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * Make payment
     *
     * @param array $params
     * @return mixed
     * @throws PaymentRequestException
     */
    public function payment(array $params)
    {
        throw new PaymentRequestException(
            'Moov Money (Flooz) payment gateway is not yet implemented. Implementation pending official API documentation.'
        );
    }

    /**
     * Make transfer
     *
     * @param array $params
     * @return mixed
     * @throws PaymentRequestException
     */
    public function transfer(array $params)
    {
        throw new PaymentRequestException(
            'Moov Money (Flooz) transfer is not yet implemented.'
        );
    }

    /**
     * Get balance
     *
     * @param array $params
     * @return mixed
     * @throws PaymentRequestException
     */
    public function balance(array $params = [])
    {
        throw new PaymentRequestException(
            'Moov Money (Flooz) balance inquiry is not yet implemented.'
        );
    }

    /**
     * Verify payment
     *
     * @param mixed array $params
     * @return mixed
     * @throws PaymentRequestException
     */
    public function verify(array $params)
    {
        throw new PaymentRequestException(
            'Moov Money (Flooz) payment verification is not yet implemented.'
        );
    }

    /**
     * Validate payment data
     *
     * @param array $params
     * @return void
     */
    public function validatePaymentData(array $params): void
    {
        // Validation logic can be implemented here as needed
    }
}
