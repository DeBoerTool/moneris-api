<?php

namespace CraigPaul\Moneris\Data;

use CraigPaul\Moneris\Support\Xml\AddXmlInterface;
use CraigPaul\Moneris\Support\DataInterface;
use SimpleXMLElement;

class CvdData implements DataInterface, AddXmlInterface
{
    public readonly int $cvdIndicator;

    public function __construct(public readonly string $value)
    {
        $this->cvdIndicator = 1;
    }

    public function toArray(): array
    {
        return [
            'cvd_info' => [
                'cvd_indicator' => $this->cvdIndicator,
                'cvd_value' => $this->value,
            ],
        ];
    }

    public function addXml(SimpleXMLElement $element): void
    {
        $cvdInfo = $element->addChild('cvd_info');
        $cvdInfo->addChild('cvd_indicator', 1);
        $cvdInfo->addChild('cvd_value', $this->value);
    }
}
