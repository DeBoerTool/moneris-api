<?php

namespace CraigPaul\Moneris\Data\Transactable;

use CraigPaul\Moneris\Data\AvsData;
use CraigPaul\Moneris\Data\Cof\CofVerificationData;
use CraigPaul\Moneris\Data\CvdData;
use CraigPaul\Moneris\Enums\DataTypeEnum;
use CraigPaul\Moneris\Interfaces\CardInterface;
use CraigPaul\Moneris\Interfaces\GatewayConfigInterface;
use CraigPaul\Moneris\Interfaces\TransactableDataInterface;
use CraigPaul\Moneris\Interfaces\TransactionProviderInterface;
use CraigPaul\Moneris\Transaction;

class VerificationData extends DataAbstract implements TransactableDataInterface, TransactionProviderInterface
{
    public function __construct(
        string $orderId,
        CardInterface $creditCard,
        CvdData|null $cvdData = null,
        AvsData|null $avsData = null,
        CofVerificationData|null $cofData = null,
    ) {
        parent::__construct(
            data: static::merge(
                [
                    'order_id' => $orderId,
                ],
                $creditCard,
                $cvdData,
                $avsData,
                $cofData,
            ),
            type: DataTypeEnum::Verification,
            useAvs: (bool) $avsData,
            useCvd: (bool) $cvdData,
            useCof: (bool) $cofData,
        );
    }

    public function getTransaction(GatewayConfigInterface $config): Transaction
    {
        return new Transaction(
            $this->setFeatures($config),
            $this,
        );
    }
}
