<?php

namespace CraigPaul\Moneris\Data\Transactable;

use CraigPaul\Moneris\Data\AvsData;
use CraigPaul\Moneris\Data\Cof\CofData;
use CraigPaul\Moneris\Data\Customer\CustomerData;
use CraigPaul\Moneris\Data\CvdData;
use CraigPaul\Moneris\Data\DataList;
use CraigPaul\Moneris\Enums\TransactionType;
use CraigPaul\Moneris\Support\Transactables\TransactableAbstract;
use CraigPaul\Moneris\Values\Amount;
use CraigPaul\Moneris\Values\DataKey;
use CraigPaul\Moneris\Values\OrderId;

class VaultPurchase extends TransactableAbstract
{
    public function __construct(
        DataKey|string $dataKey,
        OrderId|string $orderId,
        Amount|string $amount,
        CofData $cofData,
        CvdData|null $cvdData = null,
        AvsData|null $avsData = null,
        CustomerData|null $customerData = null,
        array $data = [],
    ) {
        parent::__construct(
            data: new DataList(
                DataKey::of($dataKey),
                OrderId::of($orderId),
                Amount::of($amount),
                $cofData,
                $avsData,
                $cvdData,
                $customerData,
                $data,
            ),
            type: TransactionType::VaultPurchase,
        );
    }
}
