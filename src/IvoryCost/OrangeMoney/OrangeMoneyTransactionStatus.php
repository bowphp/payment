<?php

namespace Bow\Payment\IvoryCost\OrangeMoney;

use Bow\Payment\Common\ProcessorTransactionStatusInterface;

class OrangeMoneyTransactionStatus implements ProcessorTransactionStatusInterface
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
     * Define if transaction fail
     *
     * @return bool
     */
    public function isFail()
    {
        return $this->status == 'FAIL';
    }

    /**
     * Define if transaction initiated
     *
     * @return bool
     */
    public function isInitiated()
    {
        return $this->status == 'INITIATED';
    }

    /**
     * Define if transaction expired
     *
     * @return bool
     */
    public function isExpired()
    {
        return $this->status == 'EXPIRED';
    }

    /**
     * Define if transaction success
     *
     * @return bool
     */
    public function isSuccess()
    {
        return $this->status == 'SUCCESS';
    }

    /**
     * Define if transaction pending
     *
     * @return bool
     */
    public function isPending()
    {
        return $this->status == 'PENDING';
    }
}
