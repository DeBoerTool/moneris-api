<?php

namespace CraigPaul\Moneris\Exceptions;

use Exception;

class UnsupportedTransactionException extends Exception
{
    public function __construct(string $type)
    {
        parent::__construct(sprintf(
            'Unsupported transaction type "%s".', $type),
        );
    }
}
