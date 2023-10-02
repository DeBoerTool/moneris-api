<?php

/** @noinspection PhpUnhandledExceptionInspection */

namespace CraigPaul\Moneris\Tests\Feature;

use CraigPaul\Moneris\Enums\ResponseErrorEnum;
use CraigPaul\Moneris\OldResponse;
use CraigPaul\Moneris\TestSupport\Cases\TestCase;
use CraigPaul\Moneris\Transaction;
use CraigPaul\Moneris\Values\Crypt;

/**
 * @covers \CraigPaul\Moneris\OldResponse
 */
class ResponseTest extends TestCase
{
    protected array $params;

    public function setUp(): void
    {
        parent::setUp();

        $this->params = [
            'type' => 'purchase',
            'crypt_type' => Crypt::SSL_ENABLED_MERCHANT,
            'order_id' => uniqid('1234-56789', true),
            'amount' => '1.00',
            'credit_card' => $this->visa,
            'expdate' => '2012',
        ];
    }

    /** @test */
    public function getting_a_successful_response(): void
    {
        $response = $this->gateway()->getProcessor()->process(
            new Transaction(
                config: $this->gateway()->getConfig(),
                params: $this->params,
            ),
        );

        $response = $response->validate();

        $this->assertTrue($response->isSuccessful());
    }

    /** @test */
    public function getting_a_receipt_for_a_successful_response(): void
    {
        $response = $this->gateway()->getProcessor()->process(
            new Transaction(
                config: $this->gateway()->getConfig(),
                params: $this->params,
            ),
        );

        $validated = $response->validate();

        $this->assertNotNull($validated->getReceipt());
        $this->assertSame(
            $this->params['order_id'],
            $validated->getReceipt()->read('id'),
        );
    }

    /** @test */
    public function receipt_is_null_when_unprocessed(): void
    {
        $response = new OldResponse(
            new Transaction($this->gateway()->getConfig(), []),
        );

        $this->assertNull($response->getReceipt());
    }

    /** @test */
    public function processing_expdate_error_edge_cases_from_message(): void
    {
        $response = $this->gateway()->getProcessor()->process(
            new Transaction(
                config: $this->gateway()->getConfig(),
                params: array_merge($this->params, ['expdate' => 'foo']),
            ),
        );

        $this->assertFalse($response->isSuccessful());
        $this->assertEquals(
            ResponseErrorEnum::InvalidExpiryDate,
            $response->getError(),
        );
    }

    /** @test */
    public function processing_cc_error_edge_cases_from_message(): void
    {
        $response = $this->gateway()->getProcessor()->process(
            new Transaction(
                config: $this->gateway()->getConfig(),
                params: array_merge($this->params, ['credit_card' => '1234']),
            ),
        );

        $this->assertFalse($response->isSuccessful());
        $this->assertEquals(
            ResponseErrorEnum::InvalidCard,
            $response->getError(),
        );
    }
}
