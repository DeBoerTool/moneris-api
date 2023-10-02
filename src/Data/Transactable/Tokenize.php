<?php

namespace CraigPaul\Moneris\Data\Transactable;

use CraigPaul\Moneris\Data\AvsData;
use CraigPaul\Moneris\Data\Card\CardCustomerData;
use CraigPaul\Moneris\Data\DataList;
use CraigPaul\Moneris\Enums\TransactionType;
use CraigPaul\Moneris\Support\Transactables\TransactableAbstract;
use CraigPaul\Moneris\Values\OrderId;
use CraigPaul\Moneris\Values\TransactionId;

class Tokenize extends TransactableAbstract
{
    public function __construct(
        OrderId|string $orderId,
        TransactionId|string $transactionId,
        CardCustomerData|null $cardCustomerData = null,
        AvsData|null $avsData = null,
        array $data = [],
    ) {
        parent::__construct(
            data: new DataList(
                OrderId::of($orderId),
                TransactionId::of($transactionId),
                $cardCustomerData,
                $avsData,
                $data,
            ),
            type: TransactionType::VaultTokenize,
        );
    }
}
