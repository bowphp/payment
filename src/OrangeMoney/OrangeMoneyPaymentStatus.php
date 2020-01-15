<?php

namespace Bow\Payment\OrangeMoney;

class OrangeMoneyPaymentStatus
{
    /**
    * OrangeMoneyPaymentStatus constructor
    * 
    * @param string $payment_url
    * @param string $pay_token
    * @param string $notif_token
    */
    public function __construct($payment_url, $pay_token, $notif_token)
    {
        $this->pay_token = $pay_token;
        
        $this->payment_url = $payment_url;
        
        $this->notif_token = $notif_token;
    }
    
    /**
    * Redirect client to make payment
    * 
    * @return mixed
    */
    public function redirect()
    {
        header('Location: ' . $this->payment_url);
        die();
    }
    
    /**
    * __toString
    * 
    * @return string
    */
    public function __toString()
    {
        return json_encode([
            $this->pay_token,
            $this->payment_url,
            $this->notif_token
            ]);
        }
    }
    