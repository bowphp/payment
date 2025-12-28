<?php

namespace Bow\Payment\Common;

class PaymentStatus
{
    const INITIATED = 'initiated';
    const PENDING = 'pending';
    const COMPLETED = 'completed';
    const FAILED = 'failed';
    const EXPIRED = 'expired';
    const UNKNOWN = 'unknown';
}
