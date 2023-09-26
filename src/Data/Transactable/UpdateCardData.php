<?php

namespace CraigPaul\Moneris\Data\Transactable;

use CraigPaul\Moneris\Cards\Expiry\Expiry;
use CraigPaul\Moneris\Cards\Expiry\Pan;
use CraigPaul\Moneris\Data\AvsData;
use CraigPaul\Moneris\Data\Card\CardCustomerData;
use CraigPaul\Moneris\Data\Cof\CofAddOrUpdateCardData;
use CraigPaul\Moneris\Enums\DataTypeEnum;
use CraigPaul\Moneris\Interfaces\GatewayConfigInterface;
use CraigPaul\Moneris\Interfaces\TransactableDataInterface;
use CraigPaul\Moneris\Interfaces\TransactionProviderInterface;
use CraigPaul\Moneris\Transaction;

class UpdateCardData extends DataAbstract implements TransactableDataInterface, TransactionProviderInterface
{
    public function __construct(
        string $dataKey,
        Pan $cardNumber,
        CofAddOrUpdateCardData $cofData,
        Expiry|null $expiryDate = null,
        CardCustomerData|null $cardCustomerData = null,
        protected AvsData|null $avsData = null,
        array $data = [],
    ) {
        parent::__construct(
            data: self::merge(
                $cardNumber,
                $cofData,
                $expiryDate,
                $cardCustomerData,
                $avsData,
                ['data_key' => $dataKey],
                $data,
            ),
            type: DataTypeEnum::VaultUpdateCard,
        );
    }

    public function getTransaction(GatewayConfigInterface $config): Transaction
    {
        return new Transaction(
            $config->withFeatures(
                useAvs: (bool) $this->avsData,
                // CoF is required when updating card number data.
                useCof: true,
            ),
            $this,
        );
    }
}
