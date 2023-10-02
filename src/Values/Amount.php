<?php

namespace CraigPaul\Moneris\Values;

use CraigPaul\Moneris\Support\Xml\AddXmlInterface;
use CraigPaul\Moneris\Support\DataInterface;
use CraigPaul\Moneris\Support\Values\StringValue;
use SimpleXMLElement;

class Amount extends StringValue implements DataInterface, AddXmlInterface
{
    public static function fromFloat(float $amount): static
    {
        return new static(
            number_format(
                num: $amount,
                decimals: 2,
                thousands_separator: '',
            ),
        );
    }

    public function toCaptureAmount(): CaptureAmount
    {
        return new CaptureAmount($this->value);
    }

    public function toArray(): array
    {
        return ['amount' => $this->value];
    }

    public function addXml(SimpleXMLElement $element): void
    {
        $element->addChild('amount', $this->value);
    }
}
