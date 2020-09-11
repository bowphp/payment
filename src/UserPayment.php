<?php

namespace Bow\Payment;

trait UserPayment
{
    /**
     * Make user payment
     *
     * @return mixed
     */
    public function payment($amount, $order_id, $reference)
    {
        return Bowcasher::pay($amount, $order_id, $reference);
    }
}
