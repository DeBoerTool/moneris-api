<?php

namespace CraigPaul\Moneris\Exceptions;

use CraigPaul\Moneris\Transaction;
use CraigPaul\Moneris\Validation\Errors\ErrorList;
use RuntimeException;
use Throwable;

class InvalidTransactionException extends RuntimeException
{
    public readonly ErrorList $validationErrors;

    public function __construct(
        Transaction $transaction,
        Throwable|null $previous = null,
    ) {
        $this->validationErrors = $transaction->getErrorList();

        parent::__construct(
            message: sprintf(
                'The transaction failed validation: %s',
                $this->validationErrors,
            ),
            previous: $previous,
        );
    }
}
