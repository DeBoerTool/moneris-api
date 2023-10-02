<?php

namespace CraigPaul\Moneris\Transactables;

use CraigPaul\Moneris\Cards\Expiry\Expiry;
use CraigPaul\Moneris\Cards\Expiry\Pan;
use CraigPaul\Moneris\Data\AvsData;
use CraigPaul\Moneris\Data\Card\CardCustomerData;
use CraigPaul\Moneris\Data\Cof\CofAddOrUpdateCardData;
use CraigPaul\Moneris\Data\DataList;
use CraigPaul\Moneris\Enums\TransactionType;
use CraigPaul\Moneris\Support\Transactables\TransactableAbstract;
use CraigPaul\Moneris\Values\DataKey;

class UpdateCard extends TransactableAbstract
{
    public function __construct(
        string|DataKey $dataKey,
        string|Pan $cardNumber,
        CofAddOrUpdateCardData $cofData,
        string|Expiry|null $expiryDate = null,
        CardCustomerData|null $cardCustomerData = null,
        AvsData|null $avsData = null,
        array $data = [],
    ) {
        parent::__construct(
            data: new DataList(
                $dataKey,
                $cardNumber,
                $cofData,
                $expiryDate
                    ? Expiry::of($expiryDate)
                    : null,
                $cardCustomerData,
                $avsData,
                $data,
            ),
            type: TransactionType::VaultUpdateCard,
        );
    }
}
