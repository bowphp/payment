# Orange Money API

## Configuration

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
$payment_information = $orange->getPaymentInformation();
$orange->pay(); // Redirect to payment plateforme
```

## Check payment status

```php
$token_generator = new OrangeMoneyTokenGenerator($client_key);
$amount = 1200;
$order_id = "1579565569";
$reference = 'reference';

$transaction = new OrangeMoneyTransaction($token_generator->getToken());

$orange = $transaction->check($amount, $order_id, $reference);
```

> But except that this way of doing does not allow to exploit the inheritance system in an optimal way. Use this way of doing things, only if you want to test the package or for small applications.

## Production code

```php
require __DIR__.'/vendor/autoload.php';

use Bow\Payment\OrangeMoney\OrangeMoneyPayment;
use Bow\Payment\OrangeMoney\OrangeMoneyTokenGenerator;

$client_key = "ZWZn...";
$merchant_key = 'c178...';

$token_generator = new OrangeMoneyTokenGenerator($client_key);

// Set the right production endpoint
$token_generator->setTokenGeneratorEndpoint('..');

$payment = new OrangeMoneyPayment($token_generator->getToken(), $merchant_key);

// Set the right production endpoint
$payment->setPaymentEndpoint('..');

$payment->setNotifyUrl('https://example.com/notify.php');
$payment->setCancelUrl('https://example.com/cancel.php');
$payment->setReturnUrl('https://example.com/return.php');

$amount = 1200;
$order_id = "1579565569";
$reference = 'reference';

$orange = $payment->prepare($amount, $order_id, $reference);
$payment_information = $orange->getPaymentInformation();
$orange->pay(); // Redirect to payment plateforme
```
