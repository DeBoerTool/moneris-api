<?php

namespace CraigPaul\Moneris\Data\Transactable;

use CraigPaul\Moneris\Data\AvsData;
use CraigPaul\Moneris\Data\Card\CardCustomerData;
use CraigPaul\Moneris\Enums\DataTypeEnum;
use CraigPaul\Moneris\Interfaces\GatewayConfigInterface;
use CraigPaul\Moneris\Interfaces\TransactableDataInterface;
use CraigPaul\Moneris\Interfaces\TransactionProviderInterface;
use CraigPaul\Moneris\Transaction;

class TokenizeData extends DataAbstract implements TransactableDataInterface, TransactionProviderInterface
{
    public function __construct(
        string $orderId,
        string $transactionNumber,
        CardCustomerData|null $cardCustomerData = null,
        AvsData|null $avsData = null,
        array $data = [],
    ) {
        parent::__construct(
            data: static::merge(
                [
                    'order_id' => $orderId,
                    'txn_number' => $transactionNumber,
                ],
                $cardCustomerData,
                $avsData,
                $data,
            ),
            type: DataTypeEnum::VaultTokenize,
            useAvs: (bool) $avsData,
        );
    }

    public static function fromTransaction(
        Transaction $transaction,
        CardCustomerData|null $cardCustomerData = null,
        AvsData|null $avsData = null,
        array $data = [],
    ): self {
        return new self(
            orderId: $transaction->order(),
            transactionNumber: $transaction->number(),
            cardCustomerData: $cardCustomerData,
            avsData: $avsData,
            data: $data,
        );
    }

    public function getTransaction(GatewayConfigInterface $config): Transaction
    {
        return new Transaction(
            $config->withFeatures(
                useAvs: $this->useAvs,
                useCof: true,
            ),
            $this,
        );
    }
}
