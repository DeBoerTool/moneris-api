<?php

namespace CraigPaul\Moneris\Data;

use CraigPaul\Moneris\Interfaces\DataInterface;

class AvsData implements DataInterface
{
    public function __construct(
        public readonly string $streetNumber,
        public readonly string $streetName,
        public readonly string $postalCode,
    ) {
    }

    public function toArray(): array
    {
        return [
            'avs_street_number' => $this->streetNumber,
            'avs_street_name' => $this->streetName,
            'avs_zipcode' => $this->postalCode,
        ];
    }
}
