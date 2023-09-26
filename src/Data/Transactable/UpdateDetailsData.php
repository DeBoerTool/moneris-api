<?php

namespace CraigPaul\Moneris\Data\Transactable;

use CraigPaul\Moneris\Data\AvsData;
use CraigPaul\Moneris\Data\Card\CardData;
use CraigPaul\Moneris\Enums\DataTypeEnum;
use CraigPaul\Moneris\Interfaces\GatewayConfigInterface;
use CraigPaul\Moneris\Interfaces\TransactableDataInterface;
use CraigPaul\Moneris\Interfaces\TransactionProviderInterface;
use CraigPaul\Moneris\Transaction;

class UpdateDetailsData extends DataAbstract implements TransactableDataInterface, TransactionProviderInterface
{
    public function __construct(
        string $dataKey,
        CardData $cardData,
        protected AvsData|null $avsData = null,
        array $data = [],
    ) {
        parent::__construct(
            data: self::merge(
                $cardData,
                $avsData,
                ['data_key' => $dataKey],
                $data,
            ),
            type: DataTypeEnum::VaultUpdateCard,
        );
    }

    public function getTransactableData(): array
    {
        $data = parent::getTransactableData();

        unset($data['pan']);

        return $data;
    }

    public function getTransaction(GatewayConfigInterface $config): Transaction
    {
        return new Transaction(
            // CoF is not required when updating non-card-number data.
            $config->withFeatures(useAvs: (bool) $this->avsData),
            $this,
        );
    }
}
