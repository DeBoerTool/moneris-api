<?php

namespace CraigPaul\Moneris\Data;

use CraigPaul\Moneris\Support\Xml\AddXmlInterface;
use CraigPaul\Moneris\Support\DataInterface;
use SimpleXMLElement;

class AvsData implements DataInterface, AddXmlInterface
{
    public function __construct(
        public readonly string $streetNumber,
        public readonly string $streetName,
        public readonly string $postalCode,
    ) {
    }

    public function toArray(): array
    {
        return [
            'avs_street_number' => $this->streetNumber,
            'avs_street_name' => $this->streetName,
            'avs_zipcode' => $this->postalCode,
        ];
    }

    public function addXml(SimpleXMLElement $element): void
    {
        $avs = $element->addChild('avs_info');

        foreach ($this->toArray() as $key => $value) {
            $avs->addChild($key, $value);
        }
    }
}
