<?php

namespace CraigPaul\Moneris\Data\Transactable;

use CraigPaul\Moneris\Data\AvsData;
use CraigPaul\Moneris\Data\Cof\CofData;
use CraigPaul\Moneris\Data\Customer\CustomerData;
use CraigPaul\Moneris\Data\CvdData;
use CraigPaul\Moneris\Enums\DataTypeEnum;
use CraigPaul\Moneris\Interfaces\GatewayConfigInterface;
use CraigPaul\Moneris\Interfaces\TransactableDataInterface;
use CraigPaul\Moneris\Interfaces\TransactionProviderInterface;
use CraigPaul\Moneris\Transaction;
use CraigPaul\Moneris\Values\Amount;
use CraigPaul\Moneris\Values\DataKey;
use CraigPaul\Moneris\Values\OrderId;

/**
 * A data object to represent a purchase via the Vault.
 *
 * Note that AVS is not supported for Vault purchases.
 */
class VaultPurchaseData extends DataAbstract implements TransactableDataInterface, TransactionProviderInterface
{
    public function __construct(
        DataKey|string $dataKey,
        OrderId|string $orderId,
        Amount|string $amount,
        CofData $cofData,
        CvdData|null $cvdData = null,
        AvsData|null $avsData = null,
        CustomerData|null $customerData = null,
        array $data = [],
    ) {
        parent::__construct(
            data: self::merge(
                DataKey::of($dataKey),
                OrderId::of($orderId),
                Amount::of($amount),
                $cofData,
                $avsData,
                $cvdData,
                $customerData,
                $data,
            ),
            type: DataTypeEnum::VaultPurchase,
            useAvs: (bool) $avsData,
            useCvd: (bool) $cvdData,
            useCof: true,
        );
    }

    public function getTransaction(GatewayConfigInterface $config): Transaction
    {
        return new Transaction($this->setFeatures($config), $this);
    }
}
