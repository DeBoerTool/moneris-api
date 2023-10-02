<?php

namespace CraigPaul\Moneris\Values;

use CraigPaul\Moneris\Support\Values\StringValue;
use CraigPaul\Moneris\Support\Values\ValidateInterface;
use CraigPaul\Moneris\Support\Xml\AddXmlInterface;
use InvalidArgumentException;
use SimpleXMLElement;

class CustomerId extends StringValue implements AddXmlInterface, ValidateInterface
{
    public function validate(): void
    {
        if (strpbrk($this->value, '<>$%=?^{}[]\\') !== false) {
            throw new InvalidArgumentException(
                'Customer IDs must not contain any of the following characters: < > $ % = ? ^ { } [ ] \\',
            );
        }
    }

    public function toArray(): array
    {
        return ['cust_id' => $this->value];
    }

    public function addXml(SimpleXMLElement $element): void
    {
        $element->addChild('cust_id', $this->value);
    }
}
