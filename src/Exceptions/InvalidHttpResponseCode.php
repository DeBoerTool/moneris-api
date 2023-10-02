<?php

namespace CraigPaul\Moneris\Exceptions;

use Exception;
use Throwable;

class InvalidHttpResponseCode extends Exception
{
    public function __construct(
        int $responseCode,
        int $code = 0,
        ?Throwable $previous = null,
    )
    {
        parent::__construct(
            sprintf('Invalid HTTP response code "%s".', $responseCode),
            $code,
            $previous,
        );
    }
}
