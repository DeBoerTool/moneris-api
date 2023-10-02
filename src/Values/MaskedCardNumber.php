<?php

namespace CraigPaul\Moneris\Values;

use CraigPaul\Moneris\Support\Values\StringValue;

class MaskedCardNumber extends StringValue
{
    public function __construct(string $number)
    {
        parent::__construct(sprintf(
            '%s***%s',
            substr($number, 0, 4),
            substr($number, -4)
        ));
    }
}
