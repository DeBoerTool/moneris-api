<?php

namespace CraigPaul\Moneris\Data\Transactable;

use CraigPaul\Moneris\Enums\DataTypeEnum;
use CraigPaul\Moneris\Interfaces\GatewayConfigInterface;
use CraigPaul\Moneris\Interfaces\TransactableDataInterface;
use CraigPaul\Moneris\Interfaces\TransactionProviderInterface;
use CraigPaul\Moneris\Transaction;
use CraigPaul\Moneris\Values\Amount;
use CraigPaul\Moneris\Values\OrderId;
use CraigPaul\Moneris\Values\TransactionNumber;

class RefundData extends DataAbstract implements TransactableDataInterface, TransactionProviderInterface
{
    public function __construct(
        OrderId|string $orderId,
        Amount|float|string $amount,
        string|Transaction $transaction,
        array $data = [],
    ) {
        parent::__construct(
            data: self::merge(
                OrderId::of($orderId),
                Amount::of($amount),
                TransactionNumber::of($transaction),
                $data,
            ),
            type: DataTypeEnum::Refund,
        );
    }

    public function getTransaction(GatewayConfigInterface $config): Transaction
    {
        return new Transaction($this->setFeatures($config), $this);
    }
}
