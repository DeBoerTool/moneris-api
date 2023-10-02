<?php

namespace CraigPaul\Moneris\Data\Customer;

use CraigPaul\Moneris\Data\Card\ItemDataList;
use CraigPaul\Moneris\Support\Xml\AddXmlInterface;
use CraigPaul\Moneris\Support\DataInterface;
use CraigPaul\Moneris\Data\AddressData;
use CraigPaul\Moneris\Traits\PreparableTrait;
use SimpleXMLElement;

class CustomerData implements DataInterface, AddXmlInterface
{
    use PreparableTrait;

    public function __construct(
        public readonly string $customerId,
        public readonly string $email = '',
        public readonly string $instructions = '',
        public readonly AddressData|null $billing = null,
        public readonly AddressData|null $shipping = null,
        public readonly ItemDataList|null $items = null,
    ) {
    }

    public function toArray(): array
    {
        $custInfo = [
            'email' => $this->email,
            'instructions' => $this->instructions,
        ];

        if ($this->shipping) {
            $custInfo['shipping'] = $this->shipping->toArray();
        }

        if ($this->billing) {
            $custInfo['billing'] = $this->billing->toArray();
        }

        if ($this->items) {
            $custInfo['items'] = $this->items->toArray();
        }

        return [
            'cust_id' => $this->customerId,
            'cust_info' => $custInfo,
        ];
    }

    public function addXml(SimpleXMLElement $element): void
    {
        $element->addChild('cust_id', $this->customerId);

        $custInfo = $element->addChild('cust_info');

        $custInfo->addChild('email', $this->email);

        $custInfo->addChild('instructions', $this->instructions);

        $this->billing?->addXml($custInfo->addChild('billing'));
        $this->shipping?->addXml($custInfo->addChild('shipping'));

        $this->items?->addXml($custInfo);
    }
}
