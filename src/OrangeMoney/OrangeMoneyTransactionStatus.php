<?php

namespace Bow\Payment\OrangeMoney;

class OrangeMoneyTransactionStatus
{
    /**
     * Define the transaction status
     *
     * @var string
     */
    private $status;

    /**
     * Define the transaction notif_token
     *
     * @var string
     */
    private $notif_token;

    /**
     * OrangeMoneyTransactionStatus constructor
     *
     * @param string $status
     * @param string $notif_token
     * @return void
     */
    public function __construct(string $status, string $notif_token)
    {
        $this->status = strtoupper($status);
        $this->notif_token = $notif_token;
    }

    /**
     * Get the notification token
     *
     * @return string
     */
    public function getNotificationToken()
    {
        return $this->notif_token;
    }

    /**
     * Define if transaction fail
     *
     * @return bool
     */
    public function fail()
    {
        return $this->status == 'FAIL';
    }

    /**
     * Define if transaction initiated
     *
     * @return bool
     */
    public function initiated()
    {
        return $this->status == 'INITIATED';
    }

    /**
     * Define if transaction expired
     *
     * @return bool
     */
    public function expired()
    {
        return $this->status == 'EXPIRED';
    }

    /**
     * Define if transaction success
     *
     * @return bool
     */
    public function success()
    {
        return $this->status == 'SUCCESS';
    }

    /**
     * Define if transaction pending
     *
     * @return bool
     */
    public function pending()
    {
        return $this->status == 'PENDING';
    }
}
