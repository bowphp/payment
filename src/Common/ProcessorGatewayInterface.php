<?php

namespace Bow\Payment\Common;

interface ProcessorGatewayInterface
{
    /**
     * Make payment
     * 
     * @return mixed
     */
    public function payment(...$args);

    /**
     * Make transfer
     * 
     * @return mixed
     */
    public function transfer(...$args);

    /**
     * Get balance
     * 
     * @return mixed
     */
    public function balance(...$args);

    /**
     * Verify payment
     *
     * @return void
     */
    public function verify();
}
