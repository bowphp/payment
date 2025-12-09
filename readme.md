# Bow Payment

[![Documentation](https://img.shields.io/badge/docs-read%20docs-blue.svg?style=flat-square)](https://github.com/bowphp/docs/blog/master/payment.md)
[![Latest Version](https://img.shields.io/packagist/v/bowphp/payment.svg?style=flat-square)](https://packagist.org/packages/bowphp/payment)
[![License](https://img.shields.io/github/license/mashape/apistatus.svg?style=flat-square)](https://github.com/bowphp/payment/blob/master/LICENSE)
[![Build Status](https://img.shields.io/travis/bowphp/payment/master.svg?style=flat-square)](https://travis-ci.org/bowphp/payment)

The payment gateway for Bow Framework. Built to make integrating African mobile money payment providers seamless and easy.

## Introduction

This package helps developers easily integrate local mobile payment APIs such as **Orange Money**, **Moov Money** (commonly called **Flooz**), **MTN Mobile Money**, **Wave**, and **Djamo**.

### Supported Providers

- ✅ **Orange Money** (Ivory Coast) - Fully implemented
- 🚧 **MTN Mobile Money** - In progress
- ⏳ **Moov Money (Flooz)** - Planned
- ⏳ **Wave** - Planned
- ⏳ **Djamo** - Planned

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
        'country' => 'ci',
    ],
    
    'ivory_coast' => [
        'orange' => [
            'client_key' => env('ORANGE_CLIENT_KEY'),
            'client_secret' => env('ORANGE_CLIENT_SECRET'),
            'webhook_secret' => env('ORANGE_WEBHOOK_SECRET'),
        ],
        // Other providers...
    ],
];
```

### Basic Usage

```php
use Bow\Payment\Payment;

// Configure the payment gateway
Payment::configure($config);

// Make a payment
Payment::payment([
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

## Documentation

Comprehensive documentation is available in:

- [English](./docs/en.md)
- [French](./docs/fr.md)

## Features

- ✅ Simple, fluent API
- ✅ Multiple payment provider support
- ✅ Dynamic provider switching
- ✅ Transaction status verification
- ✅ User model integration
- ✅ Webhook handling
- ✅ Transfer support
- ✅ Balance inquiry

## Requirements

- PHP >= 7.4
- Bow Framework >= 4.0
- GuzzleHTTP >= 6.5

## Testing

```bash
composer test
```

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## License

The Bow Payment package is open-sourced software licensed under the [MIT license](LICENSE).

## Support

If you find this project helpful, consider supporting its development:

[![Buy Me A Coffee](https://cdn.buymeacoffee.com/buttons/default-black.png)](https://www.buymeacoffee.com/iOLqZ3h)

## Credits

- [Franck DAKIA](https://github.com/papac) - Lead Developer
- [All Contributors](../../contributors)
