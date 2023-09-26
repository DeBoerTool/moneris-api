<?php

namespace CraigPaul\Moneris\Data\Transactable;

use CraigPaul\Moneris\Enums\DataTypeEnum;
use CraigPaul\Moneris\Interfaces\GatewayConfigInterface;
use CraigPaul\Moneris\Interfaces\TransactableDataInterface;
use CraigPaul\Moneris\Interfaces\TransactionProviderInterface;
use CraigPaul\Moneris\Transaction;

class DeleteCardData extends DataAbstract implements TransactableDataInterface, TransactionProviderInterface
{
    public function __construct(
        string $dataKey,
        array $data = [],
    ) {
        parent::__construct(
            data: static::merge(
                ['data_key' => $dataKey],
                $data,
            ),
            type: DataTypeEnum::VaultDeleteCard,
        );
    }

    public function getTransaction(GatewayConfigInterface $config): Transaction
    {
        return new Transaction($config->withFeatures(), $this);
    }
}
