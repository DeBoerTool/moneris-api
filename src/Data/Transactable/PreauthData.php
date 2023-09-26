<?php

namespace CraigPaul\Moneris\Data\Transactable;

use CraigPaul\Moneris\Data\AvsData;
use CraigPaul\Moneris\Data\Customer\CustomerData;
use CraigPaul\Moneris\Data\CvdData;
use CraigPaul\Moneris\Enums\DataTypeEnum;
use CraigPaul\Moneris\Interfaces\CardInterface;
use CraigPaul\Moneris\Interfaces\GatewayConfigInterface;
use CraigPaul\Moneris\Transaction;
use CraigPaul\Moneris\Values\Amount;
use CraigPaul\Moneris\Values\OrderId;

class PreauthData extends DataAbstract
{
    public function __construct(
        OrderId|string $orderId,
        CardInterface $creditCard,
        Amount|float|string $amount,
        CustomerData|null $customerDataData = null,
        CvdData|null $cvdData = null,
        AvsData|null $avsData = null,
        array $data = [],
    ) {
        parent::__construct(
            data: self::merge(
                OrderId::of($orderId),
                $creditCard,
                Amount::of($amount),
                $customerDataData,
                $cvdData,
                $avsData,
                $data,
            ),
            type: DataTypeEnum::Preauth,
            useAvs: (bool) $avsData,
            useCvd: (bool) $cvdData,
        );
    }

    public function getTransaction(GatewayConfigInterface $config): Transaction
    {
        return new Transaction($this->setFeatures($config), $this);
    }
}
