<?php

namespace Bow\Payment;

use Bow\Payment\Common\ProcessorGatewayInterface;

class Payment implements ProcessorGatewayInterface
{
    /**
     * Orange Money payment provider identifier
     * Used for mobile money transactions via Orange Money service
     */
    public const ORANGE = 'orange';

    /**
     * Moov Money payment provider identifier
     * Used for mobile money transactions via Moov Money service
     */
    public const MOOV = 'moov';

    /**
     * Wave payment provider identifier
     * Used for mobile money transactions via Wave service
     */
    public const WAVE = 'wave';

    /**
     * MTN Mobile Money payment provider identifier
     * Used for mobile money transactions via MTN service
     */
    public const MTN = 'mtn';

    /**
     * Djamo payment provider identifier
     * Used for digital payment transactions via Djamo service
     */
    public const DJAMO = 'djamo';

    /**
     * Ivory Coast (Côte d'Ivoire) country identifier
     * ISO 3166-1 alpha-2 country code for Ivory Coast
     */
    public const CI = 'ivory_coast';

    /**
     * Ivory Coast payment provider mapping
     * Maps payment provider identifiers to their respective service classes
     * for payment processing in Ivory Coast (CI)
     * 
     * @var array<string, class-string>
     */
    public const CI_PROVIDER = [
        Payment::ORANGE => \Bow\Payment\Gateway\IvoryCost\OrangeMoney\OrangeMoneyGateway::class,
        Payment::MTN => \Bow\Payment\Gateway\IvoryCost\MTNMobileMoney\MTNMobileMoneyGateway::class,
        Payment::MOOV => \Bow\Payment\Gateway\IvoryCost\MoovFlooz\MoovFloozGateway::class,
        Payment::WAVE => \Bow\Payment\Gateway\IvoryCost\Wave\WaveGateway::class,
        Payment::DJAMO => \Bow\Payment\Gateway\IvoryCost\Djamo\DjamoGateway::class,
    ];

    /**
     * The payment manager instance
     *
     * @var ProcessorGatewayInterface
     */
    private static $providerGateway;

    /**
     * The payment instance
     *
     * @var Payment
     */
    private static $instance;

    /**
     * ForPayment constructor
     *
     * @param array $config
     * @return mixed
     */
    public function __construct(private array $config)
    {
        $default = $this->config['default'];
        $country = $default['country'] ?? 'ci';
        $defaultProvider = $default['gateway'] ?? Payment::ORANGE;

        $this->resolveGateway($country, $defaultProvider);
    }

    /**
     * Resolve the payment gateway
     *
     * @param string $country
     * @param string $provider
     * @return void
     */
    private function resolveGateway(string $country, string $provider)
    {
        switch ($country) {
            case self::CI:
                $provider = self::CI_PROVIDER[$provider] ?? null;
                if ($provider === null) {
                    throw new \InvalidArgumentException("The payment gateway [{$provider}] is not supported in country [{$country}].");
                }
                $config = $this->resolveConfig('ivory_coast', $provider);
                static::$providerGateway = new $provider($config);
                break;
            // Other gateways can be added here
            default:
                throw new \InvalidArgumentException("The payment gateway [{$provider}] is not supported.");
        }
    }

    /**
     * Resolve configuration for a specific country and provider
     *
     * @param string $country
     * @param string $provider
     * @return array|null
     */
    public function resolveConfig(string $country, string $provider)
    {
        return $this->config[$country][$provider] ?? [];
    }

    /**
     * Make configuration
     *
     * @return PaymentManagerContract
     */
    public static function configure(array $configuration)
    {
        static::$instance = new Payment($configuration);

        return static::$instance;
    }

    /**
     * Switch payment provider
     *
     * @param string $country
     * @param string $provider
     * @return void
     */
    public function withProvider(string $country, string $provider): void
    {
        $this->resolveGateway($country, $provider);
    }

    /**
     * Make payment
     *
     * @return mixed
     */
    public function payment(...$args)
    {
        return static::$providerGateway->payment(...$args);
    }

    /**
     * Make transfer
     *
     * @return mixed
     */
    public function transfer(...$args)
    {
        return static::$providerGateway->transfer(...$args);
    }

    /**
     * Get balance
     * 
     * @return mixed
     */
    public function balance(...$args)
    {
        return static::$providerGateway->balance(...$args);
    }

    /**
     * Verify payment
     *
     * @return void
     */
    public function verify()
    {
        return static::$providerGateway->verify();
    }

    /**
     * __callStatic
     *
     * @param string $methodName
     * @param array $methodArguments
     * @return mixed
     */
    public static function __callStatic($methodName, $methodArguments)
    {
        if (method_exists(static::$instance, $methodName)) {
            return call_user_func_array([static::$instance, $methodName], $methodArguments);
        }
    }
}
