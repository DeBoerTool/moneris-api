<?php

namespace CraigPaul\Moneris\Data\Transactable;

use CraigPaul\Moneris\Data\AvsData;
use CraigPaul\Moneris\Data\Card\CardData;
use CraigPaul\Moneris\Data\Cof\CofAddOrUpdateCardData;
use CraigPaul\Moneris\Data\CvdData;
use CraigPaul\Moneris\Enums\DataTypeEnum;
use CraigPaul\Moneris\Interfaces\GatewayConfigInterface;
use CraigPaul\Moneris\Interfaces\TransactableDataInterface;
use CraigPaul\Moneris\Interfaces\TransactionProviderInterface;
use CraigPaul\Moneris\Transaction;

class AddCardData extends DataAbstract implements TransactableDataInterface, TransactionProviderInterface
{
    public function __construct(
        protected readonly CardData $cardData,
        protected readonly CofAddOrUpdateCardData $cofData,
        protected readonly CvdData|null $cvdData = null,
        protected readonly AvsData|null $avsData = null,
        array $data = [],
    ) {
        parent::__construct(
            data: static::merge(
                $cardData,
                $cofData,
                $cvdData,
                $avsData,
                $data,
            ),
            type: DataTypeEnum::VaultAddCard,
        );
    }

    public function getTransaction(GatewayConfigInterface $config): Transaction
    {
        return new Transaction(
            $config->withFeatures(
                useAvs: (bool) $this->avsData,
                useCvd: (bool) $this->cvdData,
                useCof: true,
            ),
            $this,
        );
    }
}
