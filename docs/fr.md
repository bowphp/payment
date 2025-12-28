# Documentation Bow Payment

Une passerelle de paiement complète pour Bow Framework prenant en charge plusieurs fournisseurs de mobile money africains.

## Fournisseurs Supportés

- ✅ **Orange Money** (Côte d'Ivoire) - Entièrement implémenté
- ✅ **MTN Mobile Money** (Côte d'Ivoire) - Entièrement implémenté
- 📦 **Moov Money (Flooz)** - Passerelle prête, en attente de documentation API
- 📦 **Wave** - Passerelle prête, en attente de documentation API
- 📦 **Djamo** - Passerelle prête, en attente de documentation API

## Installation

```bash
composer require bowphp/payment
```

## Démarrage Rapide

### Configuration

Configurez vos fournisseurs de paiement dans `config/payment.php`:

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
            'environment' => 'sandbox', // ou 'production'
            'webhook_secret' => env('MTN_WEBHOOK_SECRET'),
        ],
    ],
];
```

### Utilisation de Base avec Payment Facade

```php
use Bow\Payment\Payment;

// Configurer la passerelle de paiement
Payment::configure($config);

// Effectuer un paiement
$result = Payment::payment([
    'amount' => 1000,
    'reference' => 'ORDER-123',
    'notif_url' => 'https://votre-app.com/webhook',
    'return_url' => 'https://votre-app.com/success',
    'cancel_url' => 'https://votre-app.com/cancel',
]);

// Vérifier une transaction
$status = Payment::verify([
    'amount' => 1000,
    'order_id' => 'ORDER-123',
    'pay_token' => 'TOKEN',
]);

if ($status->isSuccess()) {
    // Paiement réussi
    echo "Paiement effectué avec succès!";
}
```

## Utilisation Spécifique par Fournisseur

### Orange Money

```php
Payment::configure([
    'default' => [
        'gateway' => Payment::ORANGE,
        'country' => 'ci',
    ],
    'ivory_coast' => [
        'orange' => [
            'client_key' => 'VOTRE_CLIENT_KEY',
            'client_secret' => 'VOTRE_CLIENT_SECRET',
        ],
    ],
]);

$result = Payment::payment([
    'amount' => 1000,
    'reference' => 'ORDER-123',
    'notif_url' => 'https://votre-app.com/webhook',
    'return_url' => 'https://votre-app.com/success',
    'cancel_url' => 'https://votre-app.com/cancel',
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
            'subscription_key' => 'VOTRE_SUBSCRIPTION_KEY',
            'api_user' => 'VOTRE_API_USER',
            'api_key' => 'VOTRE_API_KEY',
            'environment' => 'sandbox', // ou 'production'
        ],
    ],
]);

$result = Payment::payment([
    'amount' => 1000,
    'phone' => '0707070707',
    'reference' => 'ORDER-123',
]);

// Vérifier la transaction
$status = Payment::verify(['reference_id' => $result['reference_id']]);

// Vérifier le solde
$balance = Payment::balance();
```

### Basculer Dynamiquement entre Fournisseurs

```php
// Commencer avec Orange Money
Payment::configure($config);

// Basculer vers MTN pour une transaction spécifique
Payment::withProvider('ci', Payment::MTN);
Payment::payment($data);

// Revenir au fournisseur par défaut
Payment::withProvider('ci', Payment::ORANGE);
```

## Fonctionnalités Avancées

### Utilisation avec les Modèles

Ajoutez le trait `UserPayment` à votre modèle User:

```php
use Bow\Payment\UserPayment;

class User extends Model
{
    use UserPayment;
}

// Vous pouvez maintenant utiliser les méthodes de paiement sur votre modèle utilisateur
$user->payment(1000, 'ORDER-123');
$user->transfer(5000, 'TRANSFER-456');
$user->balance();
```

### Logique de Réessai

Réessayer automatiquement les appels API échoués avec backoff exponentiel:

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

### Limitation de Débit

Protégez votre application contre le dépassement des limites de débit de l'API:

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
    // Limite de débit dépassée, attendre avant de réessayer
    $waitTime = $limiter->getRetryAfter('orange');
}
```

### Journalisation des Transactions

Piste d'audit complète pour toutes les opérations de paiement:

```php
use Bow\Payment\Support\TransactionLogger;

$logger = new TransactionLogger('/chemin/vers/logs');

// Les journaux sont automatiquement créés avec un contexte détaillé
$logger->logPaymentRequest('mtn', [
    'amount' => 1000,
    'reference' => 'ORDER-123'
]);

$logger->logPaymentResponse('mtn', true, $response);
```

### Gestion des Webhooks

Traitement sécurisé des webhooks avec validation de signature:

```php
use Bow\Payment\Webhook\WebhookHandler;

$handler = new WebhookHandler('orange', $config['orange']['webhook_secret']);
$request = WebhookHandler::parseRequest();

$event = $handler->handle($request['payload'], $request['signature']);

if ($event->isPaymentSuccess()) {
    $transactionId = $event->getTransactionId();
    $amount = $event->getAmount();
    $status = $event->getStatus();
    
    // Mettre à jour le statut de votre commande
    Order::where('transaction_id', $transactionId)->update([
        'status' => 'paid',
        'amount' => $amount,
    ]);
}
```

## Gestion des Exceptions

Le package fournit des exceptions personnalisées complètes:

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
    // Limite de débit dépassée
    $retryAfter = $e->getCode();
    Log::warning("Limite dépassée. Réessayer après: {$retryAfter} secondes");
} catch (PaymentRequestException $e) {
    // Échec de la demande de paiement
    Log::error("Paiement échoué: " . $e->getMessage());
} catch (TokenGenerationException $e) {
    // Échec de la génération du token
    Log::error("Erreur de génération de token: " . $e->getMessage());
} catch (InvalidProviderException $e) {
    // Fournisseur invalide spécifié
    Log::error("Fournisseur invalide: " . $e->getMessage());
} catch (TransactionVerificationException $e) {
    // Échec de la vérification de transaction
    Log::error("Vérification échouée: " . $e->getMessage());
} catch (ConfigurationException $e) {
    // Erreur de configuration
    Log::error("Erreur de config: " . $e->getMessage());
}
```

## Utilisation Directe du Fournisseur (Avancé)

Pour des cas d'utilisation avancés, vous pouvez utiliser les fournisseurs directement:

### Utilisation Directe d'Orange Money

```php
use Bow\Payment\Gateway\IvoryCost\Orange\OrangeGateway;
use Bow\Payment\Gateway\IvoryCost\Orange\OrangeTokenGenerator;

$config = [
    'client_key' => 'VOTRE_CLIENT_KEY',
    'client_secret' => 'VOTRE_CLIENT_SECRET',
];

$tokenGenerator = new OrangeTokenGenerator(
    $config['client_key'],
    $config['client_secret']
);

$gateway = new OrangeGateway($tokenGenerator, $config);

$result = $gateway->payment([
    'amount' => 1000,
    'reference' => 'ORDER-123',
    'notif_url' => 'https://votre-app.com/webhook',
    'return_url' => 'https://votre-app.com/success',
    'cancel_url' => 'https://votre-app.com/cancel',
]);

// Vérifier la transaction
$status = $gateway->verify([
    'amount' => 1000,
    'order_id' => 'ORDER-123',
    'pay_token' => $result['pay_token'],
]);
```

### Utilisation Directe de MTN Mobile Money

```php
use Bow\Payment\Gateway\IvoryCost\Mono\MonoGateway;
use Bow\Payment\Gateway\IvoryCost\Mono\MomoEnvironment;
use Bow\Payment\Gateway\IvoryCost\Mono\MomoTokenGenerator;

$config = [
    'subscription_key' => 'VOTRE_SUBSCRIPTION_KEY',
    'api_user' => 'VOTRE_API_USER',
    'api_key' => 'VOTRE_API_KEY',
    'environment' => 'sandbox', // ou 'production'
];

$environment = new MomoEnvironment($config['environment']);
$tokenGenerator = new MomoTokenGenerator(
    $config['subscription_key'],
    $config['api_user'],
    $config['api_key'],
    $environment
);

$gateway = new MonoGateway($tokenGenerator, $config, $environment);

$result = $gateway->payment([
    'amount' => 1000,
    'phone' => '0707070707',
    'reference' => 'ORDER-123',
]);

// Vérifier la transaction
$status = $gateway->verify([
    'reference_id' => $result['reference_id'],
]);

// Vérifier le solde
$balance = $gateway->balance();
```

## Tests

Le package inclut des tests complets:

```bash
composer test
```

Les tests couvrent:
- Flux de paiement Orange Money
- Flux de paiement MTN Mobile Money
- Journalisation des transactions
- Logique de réessai
- Limitation de débit
- Gestion des webhooks
- Gestion des exceptions

## Exigences

- PHP >= 7.4 (PHP 8.0+ recommandé)
- Bow Framework >= 4.0
- GuzzleHTTP >= 6.5

## Contribution

Les contributions sont les bienvenues! Veuillez suivre les standards de codage PSR-12 et ajouter des tests pour les nouvelles fonctionnalités.

## Licence

Licence MIT. Voir le fichier [LICENSE](../LICENSE) pour plus de détails.
