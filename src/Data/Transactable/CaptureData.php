<?php

namespace CraigPaul\Moneris\Data\Transactable;

use CraigPaul\Moneris\Enums\DataTypeEnum;
use CraigPaul\Moneris\Interfaces\GatewayConfigInterface;
use CraigPaul\Moneris\Transaction;
use CraigPaul\Moneris\Values\Amount;
use CraigPaul\Moneris\Values\CaptureAmount;
use CraigPaul\Moneris\Values\OrderId;

class CaptureData extends DataAbstract
{
    public function __construct(
        string|Transaction $transaction,
        OrderId|string $orderId,
        Amount|CaptureAmount|float|string $amount,
        array $data = [],
    ) {
        parent::__construct(
            data: self::merge(
                OrderId::of($orderId),
                CaptureAmount::of($amount),
                [
                    'txn_number' => $transaction instanceof Transaction
                        ? $transaction->number()
                        : $transaction,
                ],
                $data,
            ),
            type: DataTypeEnum::Capture,
        );
    }

    public function getTransaction(GatewayConfigInterface $config): Transaction
    {
        return new Transaction($this->setFeatures($config), $this);
    }
}
