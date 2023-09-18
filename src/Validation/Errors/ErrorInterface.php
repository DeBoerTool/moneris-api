<?php

namespace CraigPaul\Moneris\Validation\Errors;

use JsonSerializable;

interface ErrorInterface extends JsonSerializable
{
    public function code(): int;

    public function message(): string;
}
