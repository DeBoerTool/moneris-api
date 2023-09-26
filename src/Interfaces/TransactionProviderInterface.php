<?php

namespace CraigPaul\Moneris\Interfaces;

use CraigPaul\Moneris\Transaction;

interface TransactionProviderInterface
{
    public function getTransaction(GatewayConfigInterface $config): Transaction;
}
