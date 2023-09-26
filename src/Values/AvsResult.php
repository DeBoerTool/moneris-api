<?php

namespace CraigPaul\Moneris\Values;

use CraigPaul\Moneris\Enums\AvsCode;
use CraigPaul\Moneris\Enums\CardType;

class AvsResult
{
    public function __construct(
        public readonly CardType $cardType,
        public readonly AvsCode $avsCode,
        public readonly string $message,
    ) {
    }
}
