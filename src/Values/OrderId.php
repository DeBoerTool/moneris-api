<?php

namespace CraigPaul\Moneris\Values;

use CraigPaul\Moneris\Support\Values\StringValue;
use CraigPaul\Moneris\Support\Xml\AddXmlInterface;
use CraigPaul\Moneris\Support\DataInterface;
use SimpleXMLElement;

class OrderId extends StringValue implements DataInterface, AddXmlInterface
{
    public function toArray(): array
    {
        return ['order_id' => $this->value];
    }

    public function addXml(SimpleXMLElement $element): void
    {
        $element->addChild('order_id', $this->value);
    }
}
