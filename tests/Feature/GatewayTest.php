<?php

namespace CraigPaul\Moneris\Tests\Feature;

use CraigPaul\Moneris\Tests\FeatureTestCase;
use CraigPaul\Moneris\Vault;
use Faker\Factory as Faker;

/**
 * @covers \CraigPaul\Moneris\Gateway
 */
class GatewayTest extends FeatureTestCase
{
    /**
     * The billing / shipping info for customer info requests.
     */
    protected array $billing;

    /**
     * The customer info for customer info requests.
     */
    protected array $customer;

    /**
     * The Moneris API parameters.
     */
    protected array $params;

    /**
     * Set up the test environment.
     */
    public function setUp(): void
    {
        parent::setUp();

        $faker = Faker::create();

        $this->params = [
            'order_id' => uniqid('1234-56789', true),
            'amount' => '1.00',
            'credit_card' => $this->visa,
            'expdate' => '2012',
        ];

        $this->billing = [
            'first_name' => $faker->firstName,
            'last_name' => $faker->lastName,
            'company_name' => $faker->company,
            'address' => $faker->streetAddress,
            'city' => $faker->city,
            'province' => 'SK',
            'postal_code' => 'X0X0X0',
            'country' => 'Canada',
            'phone_number' => '555-555-5555',
            'fax' => '555-555-5555',
            'tax1' => '1.01',
            'tax2' => '1.02',
            'tax3' => '1.03',
            'shipping_cost' => '9.99',
        ];
        $this->customer = [
            'email' => 'example@email.com',
            'instructions' => $faker->sentence(mt_rand(3, 6)),
            'billing' => $this->billing,
            'shipping' => $this->billing,
        ];
    }

    /** @test */
    public function instantiation(): void
    {
        $gateway = $this->gateway();

        $this->assertSame($this->id, $gateway->id);
        $this->assertSame($this->token, $gateway->token);
        $this->assertSame($this->environment, $gateway->environment);
    }

    /** @test */
    public function making_a_purchase_and_getting_a_response(): void
    {
        $response = $this->gateway()->purchase($this->params);

        $this->assertTrue($response->isSuccessful());
    }

    /** @test */
    public function making_a_purchase_with_customer_info(): void
    {
        $params = array_merge($this->params, [
            'cust_id' => uniqid('customer-', true),
            'cust_info' => $this->customer,
        ]);

        $response = $this->gateway()->purchase($params);
        $receipt = $response->getReceipt();

        $this->assertTrue($response->isSuccessful());
        $this->assertTrue($receipt->read('complete'));
        $this->assertNotNull($receipt->read('transaction'));
    }

    /** @test */
    public function making_a_purchase_with_cvd(): void
    {
        $response = $this->gateway(cvd: true)->purchase([
            'cvd' => '111',
            'order_id' => uniqid('1234-56789', true),
            'amount' => '1.00',
            'credit_card' => $this->visa,
            'expdate' => '2012',
        ]);

        $this->assertTrue($response->isSuccessful());
    }

    /** @test */
    public function making_a_purchase_with_avs(): void
    {
        $response = $this->gateway(avs: true)->purchase([
            'avs_street_number' => '123',
            'avs_street_name' => 'Fake Street',
            'avs_zipcode' => 'X0X0X0',
            'order_id' => uniqid('1234-56789', true),
            'amount' => '1.00',
            'credit_card' => $this->visa,
            'expdate' => '2012',
        ]);

        $this->assertTrue($response->isSuccessful());
    }

    /** @test */
    public function preauthorizing_a_purchase(): void
    {
        $response = $this->gateway()->preauth($this->params);

        $this->assertTrue($response->isSuccessful());
    }

    /** @test */
    public function preauth_with_customer_information(): void
    {
        $params = array_merge($this->params, [
            'cust_id' => uniqid('customer-', true),
            'cust_info' => $this->customer,
        ]);

        $response = $this->gateway()->preauth($params);
        $receipt = $response->getReceipt();

        $this->assertTrue($response->isSuccessful());
        $this->assertTrue($receipt->read('complete'));
        $this->assertNotNull($receipt->read('transaction'));
    }

    /** @test */
    public function preauth_with_cvd(): void
    {
        $response = $this->gateway(cvd: true)->preauth([
            'cvd' => '111',
            'order_id' => uniqid('1234-56789', true),
            'amount' => '1.00',
            'credit_card' => $this->visa,
            'expdate' => '2012',
        ]);

        $this->assertTrue($response->isSuccessful());
    }

    /** @test */
    public function preauth_with_avs(): void
    {
        $response = $this->gateway(avs: true)->preauth([
            'avs_street_number' => '123',
            'avs_street_name' => 'Fake Street',
            'avs_zipcode' => 'X0X0X0',
            'order_id' => uniqid('1234-56789', true),
            'amount' => '1.00',
            'credit_card' => $this->visa,
            'expdate' => '2012',
        ]);

        $this->assertTrue($response->isSuccessful());
    }

    /** @test */
    public function verifying_a_card(): void
    {
        $response = $this->gateway()->verify($this->params);

        $this->assertTrue($response->isSuccessful());
    }

    /** @test */
    public function verifying_a_cvd_secured_card(): void
    {
        $response = $this->gateway(cvd: true)->verify([
            'cvd' => '111',
            'order_id' => uniqid('1234-56789', true),
            'amount' => '1.00',
            'credit_card' => $this->visa,
            'expdate' => '2012',
        ]);

        $this->assertTrue($response->isSuccessful());
    }

    /** @test */
    public function verifying_an_avs_secured_card(): void
    {
        $response = $this->gateway(avs: true)->verify([
            'avs_street_number' => '123',
            'avs_street_name' => 'Fake Street',
            'avs_zipcode' => 'X0X0X0',
            'order_id' => uniqid('1234-56789', true),
            'amount' => '1.00',
            'credit_card' => $this->visa,
            'expdate' => '2012',
        ]);

        $this->assertTrue($response->isSuccessful());
    }

    /** @test */
    public function voiding_a_purchase(): void
    {
        $purchaseResponse = $this->gateway()->purchase($this->params);
        $voidResponse = $this->gateway()->void(
            $purchaseResponse->getTransaction(),
        );

        $this->assertTrue($voidResponse->isSuccessful());
    }

    /** @test */
    public function refunding_a_purchase(): void
    {
        $purchaseResponse = $this->gateway()->purchase($this->params);
        $refundResponse = $this->gateway()->refund(
            $purchaseResponse->getTransaction(),
        );

        $this->assertTrue($purchaseResponse->isSuccessful());
        $this->assertTrue($refundResponse->isSuccessful());
    }

    /** @test */
    public function refunding_with_a_different_amount(): void
    {
        $amount = '0.90';

        $purchaseResponse = $this->gateway()->purchase($this->params);
        $refundResponse = $this->gateway()->refund(
            transaction: $purchaseResponse->getTransaction(),
            amount: $amount,
        );

        $this->assertTrue($purchaseResponse->isSuccessful());
        $this->assertTrue($refundResponse->isSuccessful());
        $this->assertSame(
            $amount,
            $refundResponse->getReceipt()->read('amount'),
        );
    }

    /** @test */
    public function capturing_a_preauthorized_transaction(): void
    {
        $preauthResponse = $this->gateway()->preauth($this->params);
        $captureResponse = $this->gateway()->capture(
            $preauthResponse->getTransaction()
        );

        $this->assertTrue($preauthResponse->isSuccessful());
        $this->assertTrue($captureResponse->isSuccessful());
    }

    /** @test */
    public function capturing_with_a_different_amount(): void
    {
        $amount = '0.90';

        $preauth = $this->gateway()->preauth($this->params);
        $capture = $this->gateway()->capture(
            transaction: $preauth->getTransaction(),
            amount: $amount
        );

        $this->assertTrue($preauth->isSuccessful());
        $this->assertTrue($capture->isSuccessful());
        $this->assertSame(
            $amount,
            $capture->getReceipt()->read('amount'),
        );
    }

    /** @test */
    public function getting_the_vault(): void
    {
        $vault = $this->gateway()->cards();

        $this->assertInstanceOf(Vault::class, $vault);
        $this->assertSame($this->id, $vault->id);
        $this->assertSame($this->token, $vault->token);
        $this->assertSame($this->environment, $vault->environment);
    }
}
