<?php

namespace Bow\Payment\Gateway\Senegal\OrangeMoney;

use Bow\Payment\Gateway\IvoryCost\OrangeMoney\OrangeMoneyGateway as BaseOrangeMoneyGateway;

/**
 * Orange Money Gateway for Senegal
 * Orange Money operates across multiple countries
 * This class extends the base Orange Money implementation
 * 
 * The API endpoints and authentication methods are the same
 * but may use different credentials and regional configurations
 */
class OrangeMoneyGateway extends BaseOrangeMoneyGateway
{
    // Inherits all functionality from the base Orange Money gateway
    // Orange Money API is the same for Senegal and Ivory Coast
}
