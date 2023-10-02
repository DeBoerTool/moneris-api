<?php

namespace CraigPaul\Moneris\Values;

use CraigPaul\Moneris\Support\Values\StringValue;
use CraigPaul\Moneris\Support\Xml\AddXmlInterface;
use SimpleXMLElement;

class ApiToken extends StringValue implements AddXmlInterface
{
    public function addXml(SimpleXMLElement $element): void
    {
        $element->addChild('api_token', $this->value);
    }
}
