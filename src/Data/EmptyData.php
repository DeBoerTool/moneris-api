<?php

namespace CraigPaul\Moneris\Data;

use CraigPaul\Moneris\Support\Xml\AddXmlInterface;
use CraigPaul\Moneris\Support\DataInterface;
use SimpleXMLElement;

class EmptyData implements DataInterface, AddXmlInterface
{
    public function toArray(): array
    {
        return [];
    }

    public function addXml(SimpleXMLElement $element): void
    {
        // This does nothing for what I hope are obvious reasons.
    }
}
