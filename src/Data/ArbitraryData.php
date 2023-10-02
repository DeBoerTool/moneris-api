<?php

namespace CraigPaul\Moneris\Data;

use CraigPaul\Moneris\Support\Xml\AddXmlInterface;
use CraigPaul\Moneris\Support\DataInterface;
use SimpleXMLElement;

class ArbitraryData implements DataInterface, AddXmlInterface
{
    public function __construct(public readonly array $data)
    {
    }

    public function addXml(SimpleXMLElement $element): void
    {
        foreach ($this->data as $key => $value) {
            $element->addChild($key, $value);
        }
    }

    public function toArray(): array
    {
        return $this->data;
    }
}
