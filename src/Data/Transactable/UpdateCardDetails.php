<?php

namespace CraigPaul\Moneris\Data\Transactable;

use CraigPaul\Moneris\Cards\Expiry\Expiry;
use CraigPaul\Moneris\Data\AvsData;
use CraigPaul\Moneris\Data\Card\CardCustomerData;
use CraigPaul\Moneris\Data\DataList;
use CraigPaul\Moneris\Enums\TransactionType;
use CraigPaul\Moneris\Support\Transactables\TransactableAbstract;
use CraigPaul\Moneris\Values\DataKey;

class UpdateCardDetails extends TransactableAbstract
{
    public function __construct(
        string|DataKey $dataKey,
        string|Expiry|null $expiry = null,
        CardCustomerData|null $customerData = null,
        AvsData|null $avsData = null,
    ) {
        parent::__construct(
            data: new DataList(
                DataKey::of($dataKey),
                $expiry ? Expiry::of($expiry) : null,
                $customerData,
                $avsData,
            ),
            type: TransactionType::VaultUpdateCard,
        );
    }
}
