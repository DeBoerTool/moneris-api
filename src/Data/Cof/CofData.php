<?php

namespace CraigPaul\Moneris\Data\Cof;

use CraigPaul\Moneris\Enums\CofPaymentIndicator;
use CraigPaul\Moneris\Enums\CofPaymentInformation;
use CraigPaul\Moneris\Support\Xml\AddXmlInterface;
use CraigPaul\Moneris\Support\DataInterface;
use CraigPaul\Moneris\Values\IssuerId;
use SimpleXMLElement;

class CofData implements DataInterface, AddXmlInterface
{
    public function __construct(
        public readonly IssuerId|null $issuerId,
        public readonly CofPaymentInformation $paymentInformation,
        public readonly CofPaymentIndicator $paymentIndicator,
    ) {
    }

    public function toArray(): array
    {
        return [
            'issuer_id' => (string) $this->issuerId,
            'payment_indicator' => $this->paymentIndicator->value,
            'payment_information' => $this->paymentInformation->value,
        ];
    }

    public function addXml(SimpleXMLElement $element): void
    {
        $cof = $element->addChild('cof_info');

        foreach ($this->toArray() as $key => $value) {
            $cof->addChild($key, $value);
        }
    }
}
