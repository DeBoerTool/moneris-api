<?php

namespace CraigPaul\Moneris\Interfaces;

interface MonerisInterface
{
    /**
     * Create and return a new Gateway instance.
     */
    public function connect(): GatewayInterface;
}
