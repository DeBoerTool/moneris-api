<?php

namespace CraigPaul\Moneris\Validation;

use CraigPaul\Moneris\Validation\Errors\ErrorList;

interface ValidatorInterface
{
    public function passes(): bool;

    public function errors(): ErrorList;
}
