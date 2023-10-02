<?php

namespace CraigPaul\Moneris\Transactables;

use CraigPaul\Moneris\Data\AvsData;
use CraigPaul\Moneris\Data\Card\CardCustomerData;
use CraigPaul\Moneris\Data\Cof\CofAddOrUpdateCardData;
use CraigPaul\Moneris\Data\CvdData;
use CraigPaul\Moneris\Data\DataList;
use CraigPaul\Moneris\Enums\TransactionType;
use CraigPaul\Moneris\Support\Cards\CardInterface;
use CraigPaul\Moneris\Support\Transactables\TransactableAbstract;

class AddCard extends TransactableAbstract
{
    public function __construct(
        CardInterface $creditCard,
        CofAddOrUpdateCardData $cofData,
        CardCustomerData|null $cardCustomerData = null,
        CvdData|null $cvdData = null,
        AvsData|null $avsData = null,
        array $data = [],
    ) {
        parent::__construct(
            data: new DataList(
                $creditCard,
                $cofData,
                $cardCustomerData,
                $cvdData,
                $avsData,
                $data,
            ),
            type: TransactionType::VaultAddCard,
        );
    }
}
