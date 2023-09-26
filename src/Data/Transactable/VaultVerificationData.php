<?php

namespace CraigPaul\Moneris\Data\Transactable;

use CraigPaul\Moneris\Data\AvsData;
use CraigPaul\Moneris\Data\Cof\CofData;
use CraigPaul\Moneris\Data\CvdData;
use CraigPaul\Moneris\Enums\DataTypeEnum;
use CraigPaul\Moneris\Interfaces\TransactableDataInterface;

class VaultVerificationData extends DataAbstract implements TransactableDataInterface
{
    public function __construct(
        string $orderId,
        string $creditCard,
        string $expiryDate,
        CvdData|null $cvdData = null,
        AvsData|null $avsData = null,
        CofData|null $cofData = null,
    ) {
        parent::__construct(
            data: static::merge(
                [
                    'order_id' => $orderId,
                    'credit_card' => $creditCard,
                    'expdate' => $expiryDate,
                ],
                $cvdData,
                $avsData,
                $cofData,
            ),
            type: DataTypeEnum::VaultVerification,
        );
    }
}
