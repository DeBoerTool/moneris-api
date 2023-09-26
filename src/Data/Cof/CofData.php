<?php

namespace CraigPaul\Moneris\Data\Cof;

use CraigPaul\Moneris\Enums\CofPaymentIndicator;
use CraigPaul\Moneris\Enums\CofPaymentInformation;
use CraigPaul\Moneris\Interfaces\DataInterface;

class CofData implements DataInterface
{
    public function __construct(
        public readonly string $issuerId,
        public readonly CofPaymentInformation $paymentInformation,
        public readonly CofPaymentIndicator $paymentIndicator,
    ) {
    }

    public function toArray(): array
    {
        return [
            'issuer_id' => $this->issuerId,
            'payment_indicator' => $this->paymentIndicator->value,
            'payment_information' => $this->paymentInformation->value,
        ];
    }
}
