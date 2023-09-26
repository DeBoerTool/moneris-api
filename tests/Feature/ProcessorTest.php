<?php

/** @noinspection PhpUnhandledExceptionInspection */

namespace CraigPaul\Moneris\Tests\Feature;

use CraigPaul\Moneris\Exceptions\InvalidTransactionException;
use CraigPaul\Moneris\Processor;
use CraigPaul\Moneris\TestSupport\Cases\TestCase;
use CraigPaul\Moneris\Transaction;
use CraigPaul\Moneris\Values\Crypt;
use GuzzleHttp\Client;

/**
 * @covers \CraigPaul\Moneris\Processor
 */
class ProcessorTest extends TestCase
{
    protected function processor(): Processor
    {
        return new Processor(
            config: $this->gateway()->getConfig()->getConnectionConfig(),
            guzzle: new Client()
        );
    }

    /** @test */
    public function invalid_transaction_data_throws(): void
    {
        $transaction = new Transaction($this->gateway()->getConfig());

        $this->expectException(InvalidTransactionException::class);

        $this->processor()->process($transaction);
    }

    /** @test */
    public function processing_succeeds(): void
    {
        $response = $this->processor()->process(
            new Transaction(
                $this->gateway()->getConfig(),
                [
                    'type' => 'purchase',
                    'crypt_type' => Crypt::SSL_ENABLED_MERCHANT,
                    'order_id' => uniqid('1234-56789', true),
                    'amount' => '1.00',
                    'credit_card' => $this->visa,
                    'expdate' => '2012',
                ],
            ),
        );

        $this->assertTrue($response->isSuccessful());
    }

    /** @test */
    public function avs_secured_processing_succeeds(): void
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

        $this->assertTrue($response->isSuccessful());
    }

    /** @test */
    public function cvd_secured_processing_succeeds(): void
    {
        $gateway = $this->gateway(cvd: true);

        $response = $gateway->purchase([
            'order_id' => uniqid('1234-56789', true),
            'amount' => '1.00',
            'credit_card' => $this->visa,
            'expdate' => '2012',
            'cvd' => '111',
        ]);

        $this->assertTrue($response->isSuccessful());
    }
}
