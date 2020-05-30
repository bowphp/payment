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
$orange->pay(); // Redirection vers la plateforme de paiement
```

## Vérifiez le statut de paiement

```php
$token_generator = new OrangeMoneyTokenGenerator($client_key);
$amount = 1200;
$order_id = "1579565569";
$reference = 'reference';

$transaction = new OrangeMoneyTransaction($token_generator->getToken());

// Vérifiez le statut de paiement
$status = $transaction->check($amount, $order_id, $reference);
$status->pending();
$status->fail();
$status->success();

// Une autre façon de faire vérifiez le statut de paiement
$status = $transaction->checkIfHasPending($amount, $order_id, $reference);
$status = $transaction->checkIfHasSuccess($amount, $order_id, $reference);
$status = $transaction->checkIfHasFail($amount, $order_id, $reference);
```

> Mais sauf que cette façon de faire ne permet pas d'exploiter le système d'héritage de manière optimale. Utilisez cette façon de faire, uniquement si vous souhaitez tester le package ou pour de petites applications.

## Code en production

```php
require __DIR__.'/vendor/autoload.php';

use Bow\Payment\OrangeMoney\OrangeMoneyPayment;
use Bow\Payment\OrangeMoney\OrangeMoneyTokenGenerator;

$client_key = "ZWZn...";
$merchant_key = 'c178...';

$token_generator = new OrangeMoneyTokenGenerator($client_key);

// Modifier le lien pour generer le token si necessaire
$token_generator->setTokenGeneratorEndpoint('..');

$payment = new OrangeMoneyPayment($token_generator->getToken(), $merchant_key);

// Définissez le bon point de terminaison de production
$payment->setPaymentEndpoint('..');

$payment->setNotifyUrl('https://example.com/notify.php');
$payment->setCancelUrl('https://example.com/cancel.php');
$payment->setReturnUrl('https://example.com/return.php');

$amount = 1200;
$order_id = "1579565569";
$reference = 'reference';

$orange = $payment->prepare($amount, $order_id, $reference);
$payment_information = $orange->getPaymentInformation();
$orange->pay(); // Redirection vers la plateforme de paiement
```

## Check transaction

```php
// Vérifiez le statut de paiement
$transaction = new OrangeMoneyTransaction($token_generator->getToken());

// Définir l'URL de production
$transaction->setTransactionStatusEndpoint('...');

// Vérifiez le statut de paiement
$status = $transaction->check($amount, $order_id, $reference);
$status->pending();
$status->fail();
$status->success();
```
