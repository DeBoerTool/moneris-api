<?php

namespace CraigPaul\Moneris\Data\Transactable;

use CraigPaul\Moneris\Data\Customer\CustomerData;
use CraigPaul\Moneris\Data\CvdData;
use CraigPaul\Moneris\Enums\DataTypeEnum;

class VaultPreauthData extends DataAbstract
{
    public function __construct(
        string $dataKey,
        array $data,
        CustomerData|null $customer = null,
        CvdData|null $cvd = null,
    ) {
        parent::__construct(
            data: static::merge(
                $data,
                $customer,
                $cvd,
                ['data_key' => $dataKey],
            ),
            type: DataTypeEnum::VaultPreauth,
        );
    }
}
