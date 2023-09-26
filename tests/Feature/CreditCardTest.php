<?php

namespace CraigPaul\Moneris\Tests\Feature;

use CraigPaul\Moneris\Data\Card\CardCustomerData;
use CraigPaul\Moneris\Data\Transactable\AddCardData;
use CraigPaul\Moneris\TestSupport\Cases\TestCase;
use CraigPaul\Moneris\Values\Crypt;

/**
 * @covers \CraigPaul\Moneris\Data\Transactable\AddCardData
 */
class CreditCardTest extends TestCase
{
    protected AddCardData $card;

    public function setUp(): void
    {
        parent::setUp();

        $this->card = AddCardData::create($this->visa, '2012');
    }

    /** @test */
    public function instantiation(): void
    {
        $crypt = Crypt::sslEnableMerchant();

        $card = new AddCardData($this->visa, '2012', $crypt);

        $this->assertInstanceOf(AddCardData::class, $card);
        $this->assertSame($this->visa, $card->number);
        $this->assertSame('2012', $card->expiry);
        $this->assertSame($crypt, $card->crypt);
    }

    /** @test */
    public function instantiation_via_static_constructor(): void
    {
        $crypt = new Crypt(5);

        $card = AddCardData::create($this->visa, '2012', $crypt);

        $this->assertInstanceOf(AddCardData::class, $card);
        $this->assertSame($this->visa, $card->number);
        $this->assertSame('2012', $card->expiry);
        $this->assertSame($crypt, $card->crypt);
    }

    /** @test */
    public function setting_the_customer(): void
    {
        $customer = CardCustomerData::create();

        $this->card->attach($customer);

        $this->assertSame($customer, $this->card->customer);
    }
}
