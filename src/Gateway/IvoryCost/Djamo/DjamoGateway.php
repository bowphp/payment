<?php

namespace Bow\Payment\Gateway\IvoryCost\Djamo;

use Bow\Payment\Common\ProcessorGatewayInterface;
use Bow\Payment\Exceptions\PaymentRequestException;

/**
 * Djamo Gateway
 * Note: This is a placeholder implementation pending official Djamo API documentation
 */
class DjamoGateway implements ProcessorGatewayInterface
{
    /**
     * Configuration array
     *
     * @var array
     */
    private $config;

    /**
     * Create a new Djamo gateway
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
     * @param mixed ...$args
     * @return mixed
     * @throws PaymentRequestException
     */
    public function payment(...$args)
    {
        throw new PaymentRequestException(
            'Djamo payment gateway is not yet implemented. Implementation pending official API documentation.'
        );
    }

    /**
     * Make transfer
     *
     * @param mixed ...$args
     * @return mixed
     * @throws PaymentRequestException
     */
    public function transfer(...$args)
    {
        throw new PaymentRequestException(
            'Djamo transfer is not yet implemented.'
        );
    }

    /**
     * Get balance
     *
     * @param mixed ...$args
     * @return mixed
     * @throws PaymentRequestException
     */
    public function balance(...$args)
    {
        throw new PaymentRequestException(
            'Djamo balance inquiry is not yet implemented.'
        );
    }

    /**
     * Verify payment
     *
     * @param mixed ...$args
     * @return mixed
     * @throws PaymentRequestException
     */
    public function verify(...$args)
    {
        throw new PaymentRequestException(
            'Djamo payment verification is not yet implemented.'
        );
    }
}
