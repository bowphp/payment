<?php

namespace Bow\Payment;

trait Payment
{
    public function withPaymentProvider(string $country, string $provider): self
    {
        Processor::withProvider($country, $provider);

        return $this;
    }

    /**
     * Make user payment
     *
     * @return mixed
     */
    public function payment(float $amount, string $reference, array $options = [])
    {
        return Processor::payment([
            'amount' => $amount,
            'reference' => $reference,
            'options' => $options,
        ]);
    }

    /**
     * Make user payment
     *
     * @return mixed
     */
    public function transfer($amount, $reference, array $options = [])
    {
        return Processor::transfer([
            'amount' => $amount,
            'reference' => $reference,
            'options' => $options,
        ]);
    }
}
