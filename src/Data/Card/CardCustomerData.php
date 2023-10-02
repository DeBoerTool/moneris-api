<?php

namespace CraigPaul\Moneris\Data\Card;

use CraigPaul\Moneris\Support\Xml\AddXmlInterface;
use CraigPaul\Moneris\Support\DataInterface;
use CraigPaul\Moneris\Traits\PreparableTrait;
use SimpleXMLElement;

/**
 * The customer data that can be attached to Vault transactions is different
 * from the customer data that can be attached to Purchase transactions.
 */
class CardCustomerData implements DataInterface, AddXmlInterface
{
    use PreparableTrait;

    public function __construct(
        public readonly string $customerId,
        public readonly string $email,
        public readonly string $phone,
        public readonly string $note,
    ) {
    }

    public function toArray(): array
    {
        return [
            'cust_id' => $this->customerId,
            'email' => $this->email,
            'phone' => $this->phone,
            'note' => $this->note,
        ];
    }

    public function addXml(SimpleXMLElement $element): void
    {
        foreach ($this->toArray() as $key => $value) {
            $element->addChild($key, $value);
        }
    }
}
