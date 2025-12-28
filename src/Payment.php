<?php

namespace Bow\Payment;

trait Payment
{
    /**
     * Get customer phone number
     *
     * @return string
     */
    abstract public function usingCustomerPhoneNumber(): string;

    /**
     * Set payment provider
     *
     * @param string $country
     * @param string $provider
     * @return self
     */
    public function usePaymentProvider(string $country, string $provider): self
    {
        Processor::useProvider($country, $provider);

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
            'phone_number' => $this->usingCustomerPhoneNumber(),
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
            'phone_number' => $this->usingCustomerPhoneNumber(),
            'options' => $options,
        ]);
    }
}
