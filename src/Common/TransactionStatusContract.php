<?php

namespace Bow\Payment\Common;

interface TransactionStatusContract
{
    /**
     * Get the notification token
     *
     * @return string
     */
    public function getNotificationToken();

    /**
     * Define if transaction fail
     *
     * @return bool
     */
    public function fail();

    /**
     * Define if transaction initiated
     *
     * @return bool
     */
    public function initiated();

    /**
     * Define if transaction expired
     *
     * @return bool
     */
    public function expired();

    /**
     * Define if transaction success
     *
     * @return bool
     */
    public function success();

    /**
     * Define if transaction pending
     *
     * @return bool
     */
    public function pending();
}
