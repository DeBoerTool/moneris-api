<?php

namespace CraigPaul\Moneris\Values;

use SimpleXMLElement;

class CaptureAmount extends Amount
{
    public function toArray(): array
    {
        return ['comp_amount' => $this->value];
    }

    public function addXml(SimpleXMLElement $element): void
    {
        $element->addChild('comp_amount', $this->value);
    }
}
