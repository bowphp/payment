# Orange Money API

## Configuration

Vous pouvez utiliser le package simplement, comme ceci.

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
$orange->pay(); // Redirige sur la plate de paiement
```

> Mais sauf que cette façon de faire ne permet pas d'exploiter le système d'héritage de manière optimale. Utilisez cette façon de faire, uniquement si vous souhaitez tester le package ou pour de petites applications.
