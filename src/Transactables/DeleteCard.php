<?php

namespace CraigPaul\Moneris\Transactables;

use CraigPaul\Moneris\Data\DataList;
use CraigPaul\Moneris\Enums\TransactionType;
use CraigPaul\Moneris\Support\Transactables\TransactableAbstract;
use CraigPaul\Moneris\Values\DataKey;

class DeleteCard extends TransactableAbstract
{
    public function __construct(string|DataKey $dataKey) {
        parent::__construct(
            data: new DataList(DataKey::of($dataKey)),
            type: TransactionType::VaultDeleteCard,
        );
    }
}
