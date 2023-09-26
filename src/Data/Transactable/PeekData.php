<?php

namespace CraigPaul\Moneris\Data\Transactable;

use CraigPaul\Moneris\Enums\DataTypeEnum;

class PeekData extends DataAbstract
{
    public function __construct(string $dataKey)
    {
        parent::__construct(
            data: ['data_key' => $dataKey],
            type: DataTypeEnum::VaultPeek,
        );
    }
}
