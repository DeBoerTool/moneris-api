<?php

namespace CraigPaul\Moneris\Tests\Feature;

use CraigPaul\Moneris\Interfaces\GatewayInterface;
use CraigPaul\Moneris\Processor;
use CraigPaul\Moneris\Receipt;
use CraigPaul\Moneris\Response;
use CraigPaul\Moneris\Tests\TestCase;
use CraigPaul\Moneris\Transaction;
use CraigPaul\Moneris\Values\Crypt;
use GuzzleHttp\Client;

/**
 * @covers \CraigPaul\Moneris\Response
 */
class ResponseTest extends TestCase
{
    protected GatewayInterface $gateway;

    protected array $params;

    protected Processor $processor;

    protected Response $response;

    protected Transaction $transaction;

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
    public function instantiating(): void
    {
        $response = new Response($this->transaction);

        $this->assertInstanceOf(Response::class, $response);
        $this->assertSame(null, $response->status);
        $this->assertSame(true, $response->successful);
        $this->assertSame($this->transaction, $response->transaction);
    }

    /** @test */
    public function static_constructor(): void
    {
        $response = Response::create($this->transaction);

        $this->assertInstanceOf(Response::class, $response);
        $this->assertSame(null, $response->status);
        $this->assertSame(true, $response->successful);
        $this->assertSame($this->transaction, $response->transaction);
    }

    /** @test */
    public function getting_a_successful_response(): void
    {
        $response = $this->processor->process($this->transaction);

        $response = $response->validate();

        $this->assertTrue($response->successful);
    }

    /** @test */
    public function getting_a_receipt_for_a_successful_response(): void
    {
        $response = $this->processor->process($this->transaction);

        /** @var \CraigPaul\Moneris\Response $response */
        $response = $response->validate();
        $receipt = $response->receipt();

        $this->assertInstanceOf(Receipt::class, $receipt);
        $this->assertSame(
            $this->params['order_id'],
            $receipt->read('id'),
        );
    }

    /** @test */
    public function receipt_is_null_when_unprocessed(): void
    {
        $response = new Response(new Transaction($this->gateway(), []));

        $this->assertNull($response->receipt());
    }

    /** @test */
    public function processing_expdate_error_edge_cases_from_message(): void
    {
        $response = $this->processTransaction([
            'expdate' => 'foo',
        ]);

        $this->assertFalse($response->successful);
        $this->assertEquals(
            Response::INVALID_EXPIRY_DATE,
            $response->status
        );
    }

    /** @test */
    public function processing_cc_error_edge_cases_from_message(): void
    {
        $response = $this->processTransaction([
            'credit_card' => '1234',
        ]);

        $this->assertFalse($response->successful);
        $this->assertEquals(Response::INVALID_CARD, $response->status);
    }

    protected function processTransaction($params = []): Response
    {
        $this->params = array_merge($this->params, $params);
        $this->transaction = new Transaction($this->gateway, $this->params);

        $response = $this->processor->process($this->transaction);

        return $response->validate();
    }
}
