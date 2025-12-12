<?php

namespace Bow\Payment\Common;

interface ProcessorStatusInterface
{
    /**
     * Define if transaction fail
     *
     * @return bool
     */
    public function isFail();

    /**
     * Define if transaction initiated
     *
     * @return bool
     */
    public function isInitiated();

    /**
     * Define if transaction expired
     *
     * @return bool
     */
    public function isExpired();

    /**
     * Define if transaction success
     *
     * @return bool
     */
    public function isSuccess();

    /**
     * Define if transaction pending
     *
     * @return bool
     */
    public function isPending();
}
