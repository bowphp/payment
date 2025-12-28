<?php

namespace Bow\Payment;

use Bow\Payment\Common\ProcessorGatewayInterface;
use Symfony\Component\Process\Process;

class Processor implements ProcessorGatewayInterface
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
     * Senegal country identifier
     * ISO 3166-1 alpha-2 country code for Senegal
     */
    public const SN = 'senegal';

    /**
     * Ivory Coast payment provider mapping
     * Maps payment provider identifiers to their respective service classes
     * for payment processing in Ivory Coast (CI)
     * 
     * @var array<string, class-string>
     */
    public const IVORY_COAST_PROVIDER = [
        Processor::ORANGE => \Bow\Payment\Gateway\IvoryCost\Orange\OrangeGateway::class,
        Processor::MTN => \Bow\Payment\Gateway\IvoryCost\Mono\MonoGateway::class,
        Processor::MOOV => \Bow\Payment\Gateway\IvoryCost\Moov\MoovGateway::class,
        Processor::WAVE => \Bow\Payment\Gateway\IvoryCost\Wave\WaveGateway::class,
        Processor::DJAMO => \Bow\Payment\Gateway\IvoryCost\Djamo\DjamoGateway::class,
    ];

    /**
     * Senegal payment provider mapping
     * Maps payment provider identifiers to their respective service classes
     * for payment processing in Senegal (SN)
     * 
     * @var array<string, class-string>
     */
    public const SENEGAL_PROVIDER = [
        Processor::ORANGE => \Bow\Payment\Gateway\Senegal\Orange\OrangeGateway::class,
        Processor::WAVE => \Bow\Payment\Gateway\Senegal\Wave\WaveGateway::class,
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
        $country = $default['country'] ?? 'ivory_coast';
        $defaultProvider = $default['gateway'] ?? Processor::ORANGE;

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
        match($country) {
            self::CI => $this->providerFactory('ivory_coast', $provider),
            self::SN => $this->providerFactory('senegal', $provider),
            default => throw new \InvalidArgumentException("The payment gateway [{$provider}] is not supported."),
        };
    }

    /**
     * Resolve configuration for a specific country and provider
     *
     * @param string $country
     * @param string $provider
     * @return array|null
     */
    private function resolveConfig(string $country, string $provider)
    {
        return $this->config[$country][$provider] ?? [];
    }

    /**
     * Provider factory
     *
     * @param string $country
     * @param string $provider
     * @return void
     */
    private function providerFactory(string $country, string $provider): void
    {
        $provider = self::${strtoupper($country) . '_PROVIDER'}[$provider] ?? null;

        if ($provider === null) {
            throw new \InvalidArgumentException("The payment gateway [{$provider}] is not supported in country [{$country}].");
        }

        $config = $this->resolveConfig($country, $provider);

        static::$providerGateway = new $provider($config);
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
     * @param array $params
     * @return mixed
     */
    public function payment(array $params)
    {
        return static::$providerGateway->payment($params);
    }

    /**
     * Make transfer
     *
     * @param array $params
     * @return mixed
     */
    public function transfer(array $params)
    {
        return static::$providerGateway->transfer($params);
    }

    /**
     * Get balance
     * 
     * @param array $params
     * @return mixed
     */
    public function balance(array $params = [])
    {
        return static::$providerGateway->balance($params);
    }

    /**
     * Verify payment
     *
     * @param array $params
     * @return mixed
     */
    public function verify(array $params)
    {
        return static::$providerGateway->verify($params);
    }

    /**
     * Validate payment data
     * 
     * @param array $params
     * @throws InputValidationException
     * @return void
     */
    public function validatePaymentData(array $params): void
    {
        static::$providerGateway->validatePaymentData($params);
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
