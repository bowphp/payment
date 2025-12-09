<?php

namespace Bow\Payment\IvoryCost\MoovFlooz;

use Bow\Payment\Common\ProcessorGatewayInterface;
use Bow\Payment\Exceptions\PaymentRequestException;

/**
 * Moov Money (Flooz) Gateway
 * Note: This is a placeholder implementation pending official Moov Money API documentation
 */
class MoovFloozGateway implements ProcessorGatewayInterface
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
     * @param mixed ...$args
     * @return mixed
     * @throws PaymentRequestException
     */
    public function payment(...$args)
    {
        throw new PaymentRequestException(
            'Moov Money (Flooz) payment gateway is not yet implemented. Implementation pending official API documentation.'
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
            'Moov Money (Flooz) transfer is not yet implemented.'
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
            'Moov Money (Flooz) balance inquiry is not yet implemented.'
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
            'Moov Money (Flooz) payment verification is not yet implemented.'
        );
    }
}
