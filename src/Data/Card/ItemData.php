<?php

namespace CraigPaul\Moneris\Data\Card;

use CraigPaul\Moneris\Support\Xml\AddXmlInterface;
use CraigPaul\Moneris\Support\DataInterface;
use SimpleXMLElement;

class ItemData implements DataInterface, AddXmlInterface
{
    public function __construct(
        public readonly string $name = '',
        public readonly string $quantity = '',
        public readonly string $productCode = '',
        public readonly string $extendedAmount = '',
    ) {
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'quantity' => $this->quantity,
            'product_code' => $this->productCode,
            'extended_amount' => $this->extendedAmount,
        ];
    }

    public function addXml(SimpleXMLElement $element): void
    {
        $item = $element->addChild('item');

        foreach ($this->toArray() as $key => $value) {
            $item->addChild($key, $value);
        }
    }
}
