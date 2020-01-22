# Bow Payment

The paymemt gateway for Bow Framwork. Is build for make Bow Framework lovely.

## Installation

To install the package it will be better to use `composer` who is `php` package manager.

```bash
composer require bowphp/payment
```

### Configuration

You can use the package simply, like this.

```php
require __DIR__.'/vendor/autoload.php';

use Bow\Payment\OrangeMoney\OrangeMoneyPayment;
use Bow\Payment\OrangeMoney\OrangeMoneyTokenGenerator;

$client_key = "ZWZn...";
$merchant_key = 'c178...';

$token_generator = new OrangeMoneyTokenGenerator($client_key);
$payment = new OrangeMoneyPayment($token_generator->getToken(), $merchant_key);

$payment->setNotifyUrl('https://example.com/notify.php');
$payment->setCancelUrl('https://example.com/cancel.php');
$payment->setReturnUrl('https://example.com/return.php');

$amount = 1200;
$order_id = "1579565569";
$reference = 'reference';

$orange = $payment->prepare($amount, $order_id, $reference);
var_dump($orange->getPaymentInformation());
```

> But except that this way of doing does not allow to exploit the inheritance system in an optimal way. Use this way of doing things, only if you want to test the package or for small applications.
