<p align="center" style="float: right">
    <img src="http://oncodesc.com/wp-content/uploads/2017/12/save-money-icon-money_icon.png" width="150">
</p>

<p align="center" style="float: left">The paymemt gateway for Bow Framwork. Is build for make Bow Framework lovely.</p>

<p align="center">
    <a href="https://github.com/bowphp/docs/blog/master/payment.md" title="docs"><img src="https://img.shields.io/badge/docs-read%20docs-blue.svg?style=flat-square"/></a>
    <a href="https://packagist.org/packages/bowphp/payment" title="version"><img src="https://img.shields.io/packagist/v/bowphp/payment.svg?style=flat-square"/></a>
    <a href="https://github.com/bowphp/payment/blob/master/LICENSE" title="license"><img src="https://img.shields.io/github/license/mashape/apistatus.svg?style=flat-square"/></a>
    <a href="https://travis-ci.org/bowphp/payment" title="Travis branch"><img src="https://img.shields.io/travis/bowphp/payment/master.svg?style=flat-square"/></a>
</p>

## Installation

To install the package it will be better to use `composer` who is `php` package manager.

```bash
composer require bowphp/payment
```

Documentation is available in [english](./docs/en.md) and [frensh](./docs/fr.md).

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

<a href="https://www.buymeacoffee.com/iOLqZ3h" target="_blank"><img src="https://cdn.buymeacoffee.com/buttons/default-black.png" alt="Buy Me A Coffee" style="height: 30px !important; width: 150px !important;" ></a>
