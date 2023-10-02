<?php

namespace CraigPaul\Moneris\Data;

use CraigPaul\Moneris\Support\Xml\AddXmlInterface;
use CraigPaul\Moneris\Support\DataInterface;
use SimpleXMLElement;

class AddressData implements DataInterface, AddXmlInterface
{
    public function __construct(
        public readonly string $firstName = '',
        public readonly string $lastName = '',
        public readonly string $companyName = '',
        public readonly string $address = '',
        public readonly string $city = '',
        public readonly string $province = '',
        public readonly string $postalCode = '',
        public readonly string $country = '',
        public readonly string $phoneNumber = '',
        public readonly string $fax = '',
        public readonly string $tax1 = '',
        public readonly string $tax2 = '',
        public readonly string $tax3 = '',
        public readonly string $shippingCost = '',
    ) {
    }

    public function toArray(): array
    {
        return [
            'first_name' => $this->firstName,
            'last_name' => $this->lastName,
            'company_name' => $this->companyName,
            'address' => $this->address,
            'city' => $this->city,
            'province' => $this->province,
            'postal_code' => $this->postalCode,
            'country' => $this->country,
            'phone_number' => $this->phoneNumber,
            'fax' => $this->fax,
            'tax1' => $this->tax1,
            'tax2' => $this->tax2,
            'tax3' => $this->tax3,
            'shipping_cost' => $this->shippingCost,
        ];
    }

    public function addXml(SimpleXMLElement $element): void
    {
        foreach ($this->toArray() as $key => $value) {
            $element->addChild($key, $value);
        }
    }
}
