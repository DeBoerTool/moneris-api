<?php

namespace CraigPaul\Moneris\Transactables;

use CraigPaul\Moneris\Data\AvsData;
use CraigPaul\Moneris\Data\Cof\CofPurchaseData;
use CraigPaul\Moneris\Data\Customer\CustomerData;
use CraigPaul\Moneris\Data\CvdData;
use CraigPaul\Moneris\Data\DataList;
use CraigPaul\Moneris\Enums\TransactionType;
use CraigPaul\Moneris\Support\Transactables\TransactableAbstract;
use CraigPaul\Moneris\Values\Amount;
use CraigPaul\Moneris\Values\DataKey;
use CraigPaul\Moneris\Values\OrderId;

class VaultPreauth extends TransactableAbstract
{
    public function __construct(
        string|DataKey $dataKey,
        string|OrderId $orderId,
        string|Amount $amount,
        CofPurchaseData $cofData,
        CustomerData|null $customerData = null,
        CvdData|null $cvdData = null,
        AvsData|null $avsData = null,
        array $data = [],
    ) {
        parent::__construct(
            data: new DataList(
                DataKey::of($dataKey),
                OrderId::of($orderId),
                Amount::of($amount),
                $cofData,
                $customerData,
                $cvdData,
                $avsData,
                $data,
            ),
            type: TransactionType::VaultPreauth,
        );
    }
}
