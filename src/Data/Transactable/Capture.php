<?php

namespace CraigPaul\Moneris\Data\Transactable;

use CraigPaul\Moneris\Data\DataList;
use CraigPaul\Moneris\Enums\TransactionType;
use CraigPaul\Moneris\Support\Transactables\TransactableAbstract;
use CraigPaul\Moneris\Values\Amount;
use CraigPaul\Moneris\Values\CaptureAmount;
use CraigPaul\Moneris\Values\CustomerId;
use CraigPaul\Moneris\Values\OrderId;
use CraigPaul\Moneris\Values\TransactionId;

class Capture extends TransactableAbstract
{
    public function __construct(
        TransactionId|string $transactionId,
        OrderId|string $orderId,
        Amount|CaptureAmount|string $amount,
        CustomerId|string|null $customerId = null,
        array $data = [],
    ) {
        parent::__construct(
            data: new DataList(
                TransactionId::of($transactionId),
                OrderId::of($orderId),
                CaptureAmount::of($amount),
                CustomerId::optional($customerId),
                $data,
            ),
            type: TransactionType::Capture,
        );
    }
}
