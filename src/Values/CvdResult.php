<?php

namespace CraigPaul\Moneris\Values;

use CraigPaul\Moneris\Enums\CvdCode;

class CvdResult
{
    public function __construct(
        public readonly CvdCode $cvdCode,
        public readonly string $message,
    ) {
    }
}
