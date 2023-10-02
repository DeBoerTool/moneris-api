<?php

namespace CraigPaul\Moneris\Data\Transactable;

use CraigPaul\Moneris\Data\DataList;
use CraigPaul\Moneris\Enums\TransactionType;
use CraigPaul\Moneris\Support\Transactables\TransactableAbstract;
use CraigPaul\Moneris\Values\Amount;
use CraigPaul\Moneris\Values\OrderId;
use CraigPaul\Moneris\Values\TransactionId;

class Refund extends TransactableAbstract
{
    public function __construct(
        OrderId|string $orderId,
        TransactionId|string $transactionId,
        Amount|float|string $amount,
        array $data = [],
    ) {
        parent::__construct(
            data: new DataList(
                OrderId::of($orderId),
                Amount::of($amount),
                TransactionId::of($transactionId),
                $data,
            ),
            type: TransactionType::Refund,
        );
    }
}
