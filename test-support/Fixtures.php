<?php

namespace CraigPaul\Moneris\TestSupport;

use CraigPaul\Moneris\Cards\Expiry\Expiry;
use CraigPaul\Moneris\Data\AvsData;
use CraigPaul\Moneris\Data\Card\CardCustomerData;
use CraigPaul\Moneris\Data\Card\ItemData;
use CraigPaul\Moneris\Data\Card\ItemDataList;
use CraigPaul\Moneris\Data\Customer\AddressData;
use CraigPaul\Moneris\Data\Customer\CustomerData;
use CraigPaul\Moneris\Data\CvdData;
use CraigPaul\Moneris\Values\OrderId;
use Faker\Generator;
use Ramsey\Uuid\Uuid;

class Fixtures
{
    public function __construct(public readonly Generator $faker)
    {
    }

    public function cvdData(): CvdData
    {
        return new CvdData('123');
    }

    public function avsData(): AvsData
    {
        return new AvsData(
            streetNumber: '123',
            streetName: 'Fake Street',
            postalCode: 'X0X0X0',
        );
    }

    public function cardCustomerData(): CardCustomerData
    {
        return new CardCustomerData(
            customerId: uniqid('cst-', true),
            email: $this->faker->email(),
            phone: $this->faker->phoneNumber(),
            note: $this->faker->sentence(),
        );
    }

    public function addressData(): AddressData
    {
        return new AddressData(
            firstName: $this->faker->firstName(),
            lastName: $this->faker->lastName(),
            companyName: $this->faker->company(),
            address: $this->faker->streetAddress(),
            city: $this->faker->city(),
            province: 'SK',
            postalCode: 'X0X0X0',
            country: 'Canada',
            phoneNumber: '555-555-5555',
            fax: '555-555-5555',
            tax1: '1.01',
            tax2: '1.02',
            tax3: '1.03',
            shippingCost: '9.99',
        );
    }

    public function itemData(): ItemData
    {
        return new ItemData(
            name: $this->faker->sentence(),
            quantity: (string) rand(1, 10),
            productCode: $this->faker->isbn10(),
            extendedAmount: (string) $this->faker->randomFloat(
                nbMaxDecimals: 2,
                min: 0.01,
                max: 999.99,
            ),
        );
    }

    /**
     * Get customer data, fully populated.
     */
    public function customerData(string $pre = 'fcus'): CustomerData
    {
        return new CustomerData(
            customerId: uniqid(sprintf('%s-', $pre), true),
            email: $this->faker->email(),
            instructions: $this->faker->sentence(),
            billing: $this->addressData(),
            shipping: $this->addressData(),
            items: new ItemDataList(
                $this->itemData(),
                $this->itemData(),
            ),
        );
    }

    /**
     * Get customer data where all the fields are set to empty strings and
     * the items array is empty.
     */
    public function customerDataEmpty(string $pre = 'ecus'): CustomerData
    {
        return new CustomerData(
            customerId: uniqid(sprintf('%s-', $pre), true),
            email: '',
            instructions: '',
            billing: new AddressData(),
            shipping: new AddressData(),
            items: new ItemDataList(),
        );
    }

    /**
     * Get the customer data with only the customer ID set. This is the
     * minimal amount of data required.
     */
    public function customerDataMin(string $pre = 'mcus'): CustomerData
    {
        return new CustomerData(
            customerId: uniqid(sprintf('%s-', $pre), true),
        );
    }

    public function expiry(): Expiry
    {
        return new Expiry('2508');
    }

    public function orderId(): OrderId
    {
        return new OrderId(Uuid::uuid4());
    }
}
