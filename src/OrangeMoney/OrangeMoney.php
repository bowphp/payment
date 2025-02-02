<?php

namespace Bow\Payment\OrangeMoney;

class OrangeMoney
{
    /**
     * OrangeMoneyPaymentStatus constructor
     *
     * @param string $payment_url
     * @param string $pay_token
     * @param string $notif_token
     */
    public function __construct(
        private $payment_url,
        private $pay_token,
        private $notif_token
    ) {
    }

    /**
     * Redirect client to make payment
     *
     * @return mixed
     */
    public function pay()
    {
        header('Location: ' . $this->payment_url);
        die();
    }

    /**
     * Get all information information about the pending payment
     *
     * @return array
     */
    public function getPaymentInformation()
    {
        return [
            "pay_token" => $this->pay_token,
            "payment_url" => $this->payment_url,
            "notif_token" => $this->notif_token
        ];
    }

    /**
     * __toString
     *
     * @return string
     */
    public function __toString()
    {
        return json_encode($this->getPaymentInformation());
    }
}
