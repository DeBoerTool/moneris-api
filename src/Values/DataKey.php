<?php

namespace CraigPaul\Moneris\Values;

use CraigPaul\Moneris\Support\Xml\AddXmlInterface;
use CraigPaul\Moneris\Support\DataInterface;
use CraigPaul\Moneris\Support\Values\StringValue;
use SimpleXMLElement;

class DataKey extends StringValue implements DataInterface, AddXmlInterface
{
    public function toArray(): array
    {
        return ['data_key' => $this->value];
    }

    public function addXml(SimpleXMLElement $element): void
    {
        $element->addChild('data_key', $this->value);
    }
}
