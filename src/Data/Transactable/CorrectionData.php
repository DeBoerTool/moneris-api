<?php

namespace CraigPaul\Moneris\Data\Transactable;

use CraigPaul\Moneris\Enums\DataTypeEnum;
use CraigPaul\Moneris\Interfaces\GatewayConfigInterface;
use CraigPaul\Moneris\Interfaces\TransactableDataInterface;
use CraigPaul\Moneris\Interfaces\TransactionProviderInterface;
use CraigPaul\Moneris\Transaction;
use CraigPaul\Moneris\Values\CustomerId;
use CraigPaul\Moneris\Values\OrderId;
use CraigPaul\Moneris\Values\TransactionNumber;

class CorrectionData extends DataAbstract implements TransactableDataInterface, TransactionProviderInterface
{
    public function __construct(
        OrderId|string $orderId,
        string|Transaction $transaction,
        CustomerId|string|null $customerId = null,
        array $data = [],
    ) {
        parent::__construct(
            data: self::merge(
                OrderId::of($orderId),
                TransactionNumber::of($transaction),
                CustomerId::optional($customerId),
                $data,
            ),
            type: DataTypeEnum::Correction,
        );
    }

    public function getTransaction(GatewayConfigInterface $config): Transaction
    {
        return new Transaction($this->setFeatures($config), $this);
    }
}
