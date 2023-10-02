<?php

namespace CraigPaul\Moneris\Support\Transactables;

use CraigPaul\Moneris\Support\Setup\CredentialsInterface;
use CraigPaul\Moneris\Transaction;

interface TransactionProviderInterface
{
    public function getTransaction(CredentialsInterface $config): Transaction;
}
