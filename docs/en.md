# Bow Payment Documentation

A comprehensive payment gateway for Bow Framework supporting multiple African mobile money providers.

## Supported Providers

- ✅ **Orange Money** (Ivory Coast) - Fully implemented
- ✅ **MTN Mobile Money** (Ivory Coast) - Fully implemented
- 📦 **Moov Money (Flooz)** - Gateway ready, pending API documentation
- 📦 **Wave** - Gateway ready, pending API documentation
- 📦 **Djamo** - Gateway ready, pending API documentation

## Installation

```bash
composer require bowphp/payment
```

## Quick Start

### Configuration

Configure your payment providers in `config/payment.php`:

```php
use Bow\Payment\Payment;

return [
    'default' => [
        'gateway' => Payment::ORANGE,
        'country' => 'ci',
    ],
    
    'ivory_coast' => [
        'orange' => [
            'client_key' => env('ORANGE_CLIENT_KEY'),
            'client_secret' => env('ORANGE_CLIENT_SECRET'),
            'webhook_secret' => env('ORANGE_WEBHOOK_SECRET'),
        ],
        'mtn' => [
            'subscription_key' => env('MTN_SUBSCRIPTION_KEY'),
            'api_user' => env('MTN_API_USER'),
            'api_key' => env('MTN_API_KEY'),
            'environment' => 'sandbox', // or 'production'
            'webhook_secret' => env('MTN_WEBHOOK_SECRET'),
        ],
    ],
];
```

### Basic Usage with Payment Facade

```php
use Bow\Payment\Payment;

// Configure the payment gateway
Payment::configure($config);

// Make a payment
$result = Payment::payment([
    'amount' => 1000,
    'reference' => 'ORDER-123',
    'notif_url' => 'https://your-app.com/webhook',
    'return_url' => 'https://your-app.com/success',
    'cancel_url' => 'https://your-app.com/cancel',
]);

// Verify a transaction
$status = Payment::verify([
    'amount' => 1000,
    'order_id' => 'ORDER-123',
    'pay_token' => 'TOKEN',
]);

if ($status->isSuccess()) {
    // Payment successful
    echo "Payment completed!";
}
```

## Provider-Specific Usage

### Orange Money

```php
Payment::configure([
    'default' => [
        'gateway' => Payment::ORANGE,
        'country' => 'ci',
    ],
    'ivory_coast' => [
        'orange' => [
            'client_key' => 'YOUR_CLIENT_KEY',
            'client_secret' => 'YOUR_CLIENT_SECRET',
        ],
    ],
]);

$result = Payment::payment([
    'amount' => 1000,
    'reference' => 'ORDER-123',
    'notif_url' => 'https://your-app.com/webhook',
    'return_url' => 'https://your-app.com/success',
    'cancel_url' => 'https://your-app.com/cancel',
]);
```

### MTN Mobile Money

```php
Payment::configure([
    'default' => [
        'gateway' => Payment::MTN,
        'country' => 'ci',
    ],
    'ivory_coast' => [
        'mtn' => [
            'subscription_key' => 'YOUR_SUBSCRIPTION_KEY',
            'api_user' => 'YOUR_API_USER',
            'api_key' => 'YOUR_API_KEY',
            'environment' => 'sandbox', // or 'production'
        ],
    ],
]);

$result = Payment::payment([
    'amount' => 1000,
    'phone' => '0707070707',
    'reference' => 'ORDER-123',
]);

// Verify transaction
$status = Payment::verify(['reference_id' => $result['reference_id']]);

// Check balance
$balance = Payment::balance();
```

### Switching Providers Dynamically

```php
// Start with Orange Money
Payment::configure($config);

// Switch to MTN for a specific transaction
Payment::withProvider('ci', Payment::MTN);
Payment::payment($data);

// Switch back to default provider
Payment::withProvider('ci', Payment::ORANGE);
```

## Advanced Features

### Using with Models

Add the `UserPayment` trait to your User model:

```php
use Bow\Payment\UserPayment;

class User extends Model
{
    use UserPayment;
}

// Now you can use payment methods on your user model
$user->payment(1000, 'ORDER-123');
$user->transfer(5000, 'TRANSFER-456');
$user->balance();
```

### Retry Logic

Automatically retry failed API calls with exponential backoff:

```php
use Bow\Payment\Support\RetryHandler;

$retry = new RetryHandler(
    maxAttempts: 3,
    retryDelay: 1000,
    exponentialBackoff: true
);

$result = $retry->execute(function() {
    return Payment::payment([
        'amount' => 1000,
        'reference' => 'ORDER-123',
    ]);
});
```

### Rate Limiting

Protect your application from exceeding API rate limits:

```php
use Bow\Payment\Support\RateLimiter;

$limiter = new RateLimiter(
    maxRequests: 60,
    timeWindow: 60
);

if ($limiter->isAllowed('orange')) {
    $limiter->hit('orange');
    Payment::payment($data);
} else {
    // Rate limit exceeded, wait before retrying
    $waitTime = $limiter->getRetryAfter('orange');
}
```

### Transaction Logging

Comprehensive audit trail for all payment operations:

```php
use Bow\Payment\Support\TransactionLogger;

$logger = new TransactionLogger('/path/to/logs');

// Logs are automatically created with detailed context
$logger->logPaymentRequest('mtn', [
    'amount' => 1000,
    'reference' => 'ORDER-123'
]);

$logger->logPaymentResponse('mtn', true, $response);
```

### Webhook Handling

Secure webhook processing with signature validation:

```php
use Bow\Payment\Webhook\WebhookHandler;

$handler = new WebhookHandler('orange', $config['orange']['webhook_secret']);
$request = WebhookHandler::parseRequest();

$event = $handler->handle($request['payload'], $request['signature']);

if ($event->isPaymentSuccess()) {
    $transactionId = $event->getTransactionId();
    $amount = $event->getAmount();
    $status = $event->getStatus();
    
    // Update your order status
    Order::where('transaction_id', $transactionId)->update([
        'status' => 'paid',
        'amount' => $amount,
    ]);
}
```

## Exception Handling

The package provides comprehensive custom exceptions:

```php
use Bow\Payment\Exceptions\PaymentRequestException;
use Bow\Payment\Exceptions\RateLimitException;
use Bow\Payment\Exceptions\TokenGenerationException;
use Bow\Payment\Exceptions\InvalidProviderException;
use Bow\Payment\Exceptions\TransactionVerificationException;
use Bow\Payment\Exceptions\ConfigurationException;

try {
    Payment::payment($data);
} catch (RateLimitException $e) {
    // Rate limit exceeded
    $retryAfter = $e->getCode();
    Log::warning("Rate limit exceeded. Retry after: {$retryAfter} seconds");
} catch (PaymentRequestException $e) {
    // Payment request failed
    Log::error("Payment failed: " . $e->getMessage());
} catch (TokenGenerationException $e) {
    // Token generation failed
    Log::error("Token generation error: " . $e->getMessage());
} catch (InvalidProviderException $e) {
    // Invalid provider specified
    Log::error("Invalid provider: " . $e->getMessage());
} catch (TransactionVerificationException $e) {
    // Transaction verification failed
    Log::error("Verification failed: " . $e->getMessage());
} catch (ConfigurationException $e) {
    // Configuration error
    Log::error("Config error: " . $e->getMessage());
}
```

## Direct Provider Usage (Advanced)

For advanced use cases, you can use providers directly:

### Orange Money Direct Usage

```php
use Bow\Payment\IvoryCost\OrangeMoney\OrangeMoneyGateway;
use Bow\Payment\IvoryCost\OrangeMoney\OrangeMoneyTokenGenerator;

$config = [
    'client_key' => 'YOUR_CLIENT_KEY',
    'client_secret' => 'YOUR_CLIENT_SECRET',
];

$tokenGenerator = new OrangeMoneyTokenGenerator(
    $config['client_key'],
    $config['client_secret']
);

$gateway = new OrangeMoneyGateway($tokenGenerator, $config);

$result = $gateway->payment([
    'amount' => 1000,
    'reference' => 'ORDER-123',
    'notif_url' => 'https://your-app.com/webhook',
    'return_url' => 'https://your-app.com/success',
    'cancel_url' => 'https://your-app.com/cancel',
]);

// Verify transaction
$status = $gateway->verify([
    'amount' => 1000,
    'order_id' => 'ORDER-123',
    'pay_token' => $result['pay_token'],
]);
```

### MTN Mobile Money Direct Usage

```php
use Bow\Payment\IvoryCost\MTNMobileMoney\MTNMobileMoneyGateway;
use Bow\Payment\IvoryCost\MTNMobileMoney\MomoEnvironment;
use Bow\Payment\IvoryCost\MTNMobileMoney\MomoTokenGenerator;

$config = [
    'subscription_key' => 'YOUR_SUBSCRIPTION_KEY',
    'api_user' => 'YOUR_API_USER',
    'api_key' => 'YOUR_API_KEY',
    'environment' => 'sandbox', // or 'production'
];

$environment = new MomoEnvironment($config['environment']);
$tokenGenerator = new MomoTokenGenerator(
    $config['subscription_key'],
    $config['api_user'],
    $config['api_key'],
    $environment
);

$gateway = new MTNMobileMoneyGateway($tokenGenerator, $config, $environment);

$result = $gateway->payment([
    'amount' => 1000,
    'phone' => '0707070707',
    'reference' => 'ORDER-123',
]);

// Verify transaction
$status = $gateway->verify([
    'reference_id' => $result['reference_id'],
]);

// Check balance
$balance = $gateway->balance();
```

## Testing

The package includes comprehensive tests:

```bash
composer test
```

Tests cover:
- Orange Money payment flow
- MTN Mobile Money payment flow
- Transaction logging
- Retry logic
- Rate limiting
- Webhook handling
- Exception handling

## Requirements

- PHP >= 7.4 (PHP 8.0+ recommended)
- Bow Framework >= 4.0
- GuzzleHTTP >= 6.5

## Contributing

Contributions are welcome! Please follow PSR-12 coding standards and add tests for new features.

## License

MIT License. See [LICENSE](../LICENSE) file for details.
