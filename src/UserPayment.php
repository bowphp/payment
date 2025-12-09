<?php

namespace Bow\Payment;

trait UserPayment
{
    public function withPaymentProvider(string $country, string $provider): self
    {
        Payment::withProvider($country, $provider);

        return $this;
    }

    /**
     * Make user payment
     *
     * @return mixed
     */
    public function payment($amount, $reference)
    {
        return Payment::payment($amount, $reference);
    }

    /**
     * Make user payment
     *
     * @return mixed
     */
    public function transfer($amount, $reference)
    {
        return Payment::transfer($amount, $reference);
    }
}
