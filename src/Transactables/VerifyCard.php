<?php

namespace CraigPaul\Moneris\Transactables;

use CraigPaul\Moneris\Data\AvsData;
use CraigPaul\Moneris\Data\Cof\CofVerificationData;
use CraigPaul\Moneris\Data\CvdData;
use CraigPaul\Moneris\Data\DataList;
use CraigPaul\Moneris\Enums\TransactionType;
use CraigPaul\Moneris\Support\Cards\CardInterface;
use CraigPaul\Moneris\Support\Transactables\TransactableAbstract;
use CraigPaul\Moneris\Values\OrderId;

class VerifyCard extends TransactableAbstract
{
    public function __construct(
        string|OrderId $orderId,
        CardInterface $creditCard,
        CvdData|null $cvdData = null,
        AvsData|null $avsData = null,
        CofVerificationData|null $cofData = null,
    ) {
        parent::__construct(
            data: new DataList(
                OrderId::of($orderId),
                $creditCard,
                $cvdData,
                $avsData,
                $cofData,
            ),
            type: TransactionType::Verification,
        );
    }
}
