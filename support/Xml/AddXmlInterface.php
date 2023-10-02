<?php

namespace CraigPaul\Moneris\Support\Xml;

use SimpleXMLElement;

interface AddXmlInterface
{
    public function addXml(SimpleXMLElement $element): void;
}
