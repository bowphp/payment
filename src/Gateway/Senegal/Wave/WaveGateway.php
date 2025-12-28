<?php

namespace Bow\Payment\Gateway\Senegal\Wave;

use Bow\Payment\Shared\Wave\WaveGateway as SharedWaveGateway;

/**
 * Wave Gateway for Senegal
 * Wave operates across multiple countries with the same API
 * This class extends the base Wave implementation
 * 
 * @link https://docs.wave.com/checkout
 */
class WaveGateway extends SharedWaveGateway
{
    // Inherits all functionality from the base Wave gateway
    // Wave API is the same for Senegal and Ivory Coast
}
