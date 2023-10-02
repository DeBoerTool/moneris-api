<?php

namespace CraigPaul\Moneris\Data\Transactable;

use CraigPaul\Moneris\Data\DataList;
use CraigPaul\Moneris\Enums\TransactionType;
use CraigPaul\Moneris\Support\Transactables\TransactableAbstract;
use CraigPaul\Moneris\Transaction;
use CraigPaul\Moneris\Values\CustomerId;
use CraigPaul\Moneris\Values\OrderId;
use CraigPaul\Moneris\Values\TransactionId;

class Correction extends TransactableAbstract
{
    public function __construct(
        OrderId|string $orderId,
        Transaction|string $transaction,
        CustomerId|string|null $customerId = null,
        array $data = [],
    ) {
        parent::__construct(
            data: new DataList(
                OrderId::of($orderId),
                TransactionId::of($transaction),
                CustomerId::optional($customerId),
                $data,
            ),
            type: TransactionType::Correction,
        );
    }
}
