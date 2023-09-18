<?php

namespace CraigPaul\Moneris\Tests\Feature;

use CraigPaul\Moneris\CreditCard;
use CraigPaul\Moneris\Customer;
use CraigPaul\Moneris\Tests\FeatureTestCase;
use CraigPaul\Moneris\Values\Crypt;

/**
 * @covers \CraigPaul\Moneris\CreditCard
 */
class CreditCardTest extends FeatureTestCase
{
    protected CreditCard $card;

    public function setUp(): void
    {
        parent::setUp();

        $this->card = CreditCard::create($this->visa, '2012');
    }

    /** @test */
    public function instantiation(): void
    {
        $crypt = Crypt::sslEnableMerchant();

        $card = new CreditCard($this->visa, '2012', $crypt);

        $this->assertInstanceOf(CreditCard::class, $card);
        $this->assertSame($this->visa, $card->number);
        $this->assertSame('2012', $card->expiry);
        $this->assertSame($crypt, $card->crypt);
    }

    /** @test */
    public function instantiation_via_static_constructor(): void
    {
        $crypt = new Crypt(5);

        $card = CreditCard::create($this->visa, '2012', $crypt);

        $this->assertInstanceOf(CreditCard::class, $card);
        $this->assertSame($this->visa, $card->number);
        $this->assertSame('2012', $card->expiry);
        $this->assertSame($crypt, $card->crypt);
    }

    /** @test */
    public function setting_the_customer(): void
    {
        $customer = Customer::create();

        $this->card->attach($customer);

        $this->assertSame($customer, $this->card->customer);
    }
}
