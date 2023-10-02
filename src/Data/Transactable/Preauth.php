<?php

namespace CraigPaul\Moneris\Data\Transactable;

use CraigPaul\Moneris\Data\AvsData;
use CraigPaul\Moneris\Data\Customer\CustomerData;
use CraigPaul\Moneris\Data\CvdData;
use CraigPaul\Moneris\Data\DataList;
use CraigPaul\Moneris\Enums\TransactionType;
use CraigPaul\Moneris\Support\Cards\CardInterface;
use CraigPaul\Moneris\Support\Transactables\TransactableAbstract;
use CraigPaul\Moneris\Values\Amount;
use CraigPaul\Moneris\Values\OrderId;

class Preauth extends TransactableAbstract
{
    public function __construct(
        OrderId|string $orderId,
        CardInterface $creditCard,
        Amount|string $amount,
        CustomerData|null $customerData = null,
        CvdData|null $cvdData = null,
        AvsData|null $avsData = null,
        array $data = [],
    ) {
        parent::__construct(
            data: new DataList(
                OrderId::of($orderId),
                $creditCard,
                Amount::of($amount),
                $customerData,
                $cvdData,
                $avsData,
                $data,
            ),
            type: TransactionType::Preauth,
        );
    }
}
