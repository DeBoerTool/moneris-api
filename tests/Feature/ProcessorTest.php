<?php

namespace CraigPaul\Moneris\Tests\Feature;

use CraigPaul\Moneris\Interfaces\GatewayInterface;
use CraigPaul\Moneris\Processor;
use CraigPaul\Moneris\Response;
use CraigPaul\Moneris\Tests\TestCase;
use CraigPaul\Moneris\Transaction;
use CraigPaul\Moneris\Values\Crypt;
use GuzzleHttp\Client;

/**
 * @covers \CraigPaul\Moneris\Processor
 */
class ProcessorTest extends TestCase
{
    protected GatewayInterface $gateway;

    protected Processor $processor;

    protected Transaction $transaction;

    protected array $params;

    public function setUp(): void
    {
        parent::setUp();

        $this->gateway = $this->gateway();

        $this->params = [
            'type' => 'purchase',
            'crypt_type' => Crypt::SSL_ENABLED_MERCHANT,
            'order_id' => uniqid('1234-56789', true),
            'amount' => '1.00',
            'credit_card' => $this->visa,
            'expdate' => '2012',
        ];

        $this->transaction = new Transaction($this->gateway, $this->params);
        $this->processor = new Processor(new Client());
    }

    /** @test */
    public function it_can_instantiate_via_the_constructor()
    {
        $processor = new Processor(new Client());

        $this->assertEquals(Processor::class, get_class($processor));
    }

    /** @test */
    public function it_responds_to_an_invalid_transaction_with_the_proper_code_and_status()
    {
        $transaction = new Transaction($this->gateway);

        $response = $this->processor->process($transaction);

        $this->assertFalse($response->successful);
        $this->assertEquals(Response::INVALID_TRANSACTION_DATA, $response->status);
    }

    /** @test */
    public function it_can_submit_a_proper_request_to_the_moneris_api()
    {
        $response = $this->processor->process($this->transaction);

        $this->assertTrue($response->successful);
    }

    /** @test */
    public function it_can_submit_a_avs_secured_request_to_the_moneris_api()
    {
        $gateway = $this->gateway(avs: true);
        $response = $gateway->purchase([
            'order_id' => uniqid('1234-56789', true),
            'amount' => '1.00',
            'credit_card' => $this->visa,
            'expdate' => '2012',
            'avs_street_number' => '123',
            'avs_street_name' => 'Fake Street',
            'avs_zipcode' => 'X0X0X0',
        ]);

        $this->assertTrue($response->successful);
    }

    /** @test */
    public function it_can_submit_a_cvd_secured_request_to_the_moneris_api()
    {
        $gateway = $this->gateway(cvd: true);
        $response = $gateway->purchase([
            'order_id' => uniqid('1234-56789', true),
            'amount' => '1.00',
            'credit_card' => $this->visa,
            'expdate' => '2012',
            'cvd' => '111',
        ]);

        $this->assertTrue($response->successful);
    }
}
