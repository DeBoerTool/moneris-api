<?php

namespace CraigPaul\Moneris\Tests\Feature;

use CraigPaul\Moneris\Tests\FeatureTestCase;

/**
 * @covers \CraigPaul\Moneris\Receipt
 */
class ReceiptTest extends FeatureTestCase
{
    /** @test */
    public function serializing_to_json(): void
    {
        $response = $this->gateway()->purchase([
            'order_id' => uniqid('1234-56789', true),
            'amount' => '1.00',
            'credit_card' => $this->visa,
            'expdate' => '2012',
        ]);

        $receiptData = json_decode(
            json_encode($response->receipt()),
            associative: true
        );

        $this->assertSame($response->receipt()->getData(), $receiptData);
    }
}
