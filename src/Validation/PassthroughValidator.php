<?php

namespace CraigPaul\Moneris\Validation;

use CraigPaul\Moneris\Validation\Errors\ErrorList;

class PassthroughValidator implements ValidatorInterface
{
    public function passes(): bool
    {
        return true;
    }

    public function errors(): ErrorList
    {
        return new ErrorList();
    }
}
