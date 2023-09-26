<?php

namespace CraigPaul\Moneris\Data;

use CraigPaul\Moneris\Interfaces\DataInterface;

class CvdData implements DataInterface
{
    public function __construct(public readonly string $cvd)
    {
    }

    public function toArray(): array
    {
        return [
            'cvd_info' => [
                'cvd_indicator' => 1,
                'cvd_value' => $this->cvd,
            ],
        ];
    }
}
