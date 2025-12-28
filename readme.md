# Bow Payment

[![Documentation](https://img.shields.io/badge/docs-read%20docs-blue.svg?style=flat-square)](https://github.com/bowphp/docs/blog/master/payment.md)
[![Latest Version](https://img.shields.io/packagist/v/bowphp/payment.svg?style=flat-square)](https://packagist.org/packages/bowphp/payment)
[![License](https://img.shields.io/github/license/mashape/apistatus.svg?style=flat-square)](https://github.com/bowphp/payment/blob/master/LICENSE)
[![Build Status](https://img.shields.io/travis/bowphp/payment/master.svg?style=flat-square)](https://travis-ci.org/bowphp/payment)

The comprehensive payment gateway for Bow Framework. Built to make integrating African mobile money payment providers seamless, secure, and reliable.

## Introduction

This package helps developers easily integrate local mobile payment APIs such as **Orange Money**, **Moov Money** (commonly called **Flooz**), **MTN Mobile Money**, **Wave**, and **Djamo** with advanced features like retry logic, rate limiting, transaction logging, and webhook handling.

### Supported Providers

- ✅ **Orange Money** (Ivory Coast) - Fully implemented
- ✅ **MTN Mobile Money** (Ivory Coast) - Fully implemented
- 📦 **Moov Money (Flooz)** - Gateway ready, pending API documentation
- 📦 **Wave** - Gateway ready, pending API documentation
- 📦 **Djamo** - Gateway ready, pending API documentation

## Installation

Install the package using `composer`, the PHP package manager:

```bash
composer require bowphp/payment
```

## Quick Start

### Configuration

Configure your payment providers in your `config/payment.php`:

```php
use Bow\Payment\Payment;

return [
    'default' => [
        'gateway' => Payment::ORANGE,
        'country' => 'ivory_coast',
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
        // Other providers...
    ],
];
```

### Basic Usage

```php
use Bow\Payment\Payment;

// Configure the payment gateway
$gateway = Payment::configure($config);

// Make a payment
$gateway->payment([
    'amount' => 1000,
    'phone_number' => '+225070000001',
    'reference' => 'ORDER-123',
    'options' => [
        'notif_url' => 'https://your-app.com/webhook',
        'return_url' => 'https://your-app.com/success',
        'cancel_url' => 'https://your-app.com/cancel',
    ]
]);

// Verify a transaction
$status = $gateway->verify([
    'reference' => 'ORDER-123',
    'options' => [
        // 
    ]
]);

if ($status->isSuccess()) {
    // Payment successful
}
```

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
```

## Advanced Features

### Retry Logic

Automatically retry failed API calls with exponential backoff:

```php
use Bow\Payment\Support\RetryHandler;

$retry = new RetryHandler(
    maxAttempts: 3,
    retryDelay: 1000,
    exponentialBackoff: true
);

$result = $retry->execute(function() use ($payment) {
    return $payment->pay($amount);
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
    // Make API call
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

$handler = new WebhookHandler('orange', $config['webhook_secret']);
$request = WebhookHandler::parseRequest();

$event = $handler->handle($request['payload'], $request['signature']);

if ($event->isPaymentSuccess()) {
    $transactionId = $event->getTransactionId();
    $amount = $event->getAmount();
    // Update order status
}
```

### Exception Handling

Comprehensive custom exceptions for better error handling:

```php
use Bow\Payment\Exceptions\PaymentRequestException;
use Bow\Payment\Exceptions\RateLimitException;
use Bow\Payment\Exceptions\TokenGenerationException;

try {
    Payment::payment($data);
} catch (RateLimitException $e) {
    // Rate limit exceeded
    $retryAfter = $e->getCode();
} catch (PaymentRequestException $e) {
    // Payment request failed
    Log::error($e->getMessage());
} catch (TokenGenerationException $e) {
    // Token generation failed
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
    'options' => [
        'notif_url' => 'https://your-app.com/webhook',
        'return_url' => 'https://your-app.com/success',
        'cancel_url' => 'https://your-app.com/cancel',
    ],
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
            'environment' => 'sandbox',
        ],
    ],
]);

$result = Payment::payment([
    'amount' => 1000,
    'reference' => 'ORDER-123',
    'phone_number' => '0707070707',
]);

// Verify transaction
$status = Payment::verify(['reference' => $result['reference']]);

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
```

## Features

- ✅ Simple, fluent API
- ✅ Multiple payment provider support (Orange Money, MTN Mobile Money)
- ✅ Dynamic provider switching
- ✅ Transaction status verification
- ✅ User model integration via traits
- ✅ Webhook handling with signature validation
- ✅ Transfer support
- ✅ Balance inquiry
- ✅ Automatic retry logic with exponential backoff
- ✅ Rate limiting protection
- ✅ Transaction audit logging
- ✅ Comprehensive exception handling
- ✅ Sandbox and production environment support

## Requirements

- PHP >= 7.4 (PHP 8.0+ recommended)
- Bow Framework >= 4.0
- GuzzleHTTP >= 6.5

## Testing

Run the test suite:

```bash
composer test
```

The package includes comprehensive tests for:
- Transaction logging
- Retry logic
- Rate limiting
- Webhook handling
- Payment configuration

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

### Development Guidelines

1. Follow PSR-12 coding standards
2. Add tests for new features
3. Update documentation
4. Ensure all tests pass before submitting PR

## Changelog

See [UPGRADE_SUMMARY.md](UPGRADE_SUMMARY.md) for recent changes and improvements.

## License

The Bow Payment package is open-sourced software licensed under the [MIT license](LICENSE).

## Support

If you find this project helpful, consider supporting its development:

[![Buy Me A Coffee](https://cdn.buymeacoffee.com/buttons/default-black.png)](https://www.buymeacoffee.com/iOLqZ3h)

## Credits

- [Franck DAKIA](https://github.com/papac) - Lead Developer
- [All Contributors](../../contributors)
