<?php

namespace CraigPaul\Moneris\Data;

use CraigPaul\Moneris\Interfaces\DataInterface;

class EmptyData implements DataInterface
{
    public function toArray(): array
    {
        return [];
    }
}
