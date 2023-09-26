<?php

namespace CraigPaul\Moneris\Data\Cof;

use CraigPaul\Moneris\Enums\CofPaymentIndicator;
use CraigPaul\Moneris\Enums\CofPaymentInformation;

class CofPurchaseData extends CofData
{
    public function __construct(string $issuerId)
    {
        parent::__construct(
            issuerId: $issuerId,
            paymentInformation: CofPaymentInformation::Subsequent,
            paymentIndicator: CofPaymentIndicator::Unscheduled,
        );
    }
}
