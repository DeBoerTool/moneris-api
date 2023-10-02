<?php

namespace CraigPaul\Moneris\Data\Cof;

use CraigPaul\Moneris\Enums\CofPaymentIndicator;
use CraigPaul\Moneris\Enums\CofPaymentInformation;
use CraigPaul\Moneris\Values\IssuerId;

class CofPurchaseData extends CofData
{
    public function __construct(IssuerId|string|null $issuerId)
    {
        parent::__construct(
            issuerId: IssuerId::optional($issuerId),
            paymentInformation: CofPaymentInformation::Subsequent,
            paymentIndicator: CofPaymentIndicator::Unscheduled,
        );
    }
}
