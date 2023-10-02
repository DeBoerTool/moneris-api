<?php

namespace CraigPaul\Moneris\Values;

use CraigPaul\Moneris\Support\Values\StringValue;
use CraigPaul\Moneris\Support\Xml\AddXmlInterface;
use SimpleXMLElement;

class TransactionId extends StringValue implements AddXmlInterface
{
    public function toArray(): array
    {
        return ['txn_number' => $this->value];
    }

    public function addXml(SimpleXMLElement $element): void
    {
        $element->addChild('txn_number', $this->value);
    }
}
