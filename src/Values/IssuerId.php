<?php

namespace CraigPaul\Moneris\Values;

use CraigPaul\Moneris\Support\Xml\AddXmlInterface;
use CraigPaul\Moneris\Support\DataInterface;
use CraigPaul\Moneris\Support\Values\StringValue;
use SimpleXMLElement;

class IssuerId extends StringValue implements DataInterface, AddXmlInterface
{
    public function toArray(): array
    {
        return ['issuer_id' => $this->value];
    }

    public function addXml(SimpleXMLElement $element): void
    {
        $element->addChild('issuer_id', $this->value);
    }
}
