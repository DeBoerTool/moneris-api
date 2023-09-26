<?php

namespace CraigPaul\Moneris\Values;

use Stringable;

class MaskedCardNumber implements Stringable
{
    public readonly string $number;

    public function __construct(string $number)
    {
        $this->number = sprintf(
            '%s***%s',
            substr($number, 0, 4),
            substr($number, -4)
        );
    }

    public function __toString(): string
    {
        return $this->number;
    }
}
