<?php

namespace CraigPaul\Moneris\Values;

use CraigPaul\Moneris\Support\Values\StringValue;
use CraigPaul\Moneris\Support\Xml\AddXmlInterface;
use SimpleXMLElement;

class StoreId extends StringValue implements AddXmlInterface
{
    public function addXml(SimpleXMLElement $element): void
    {
        $element->addChild('store_id', $this->value);
    }
}
