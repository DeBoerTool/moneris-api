<?php

namespace CraigPaul\Moneris\Tests\Feature;

use PHPUnit\Framework\Attributes\CoversClass;
use CraigPaul\Moneris\Receipt;
use PHPUnit\Framework\Attributes\Test;
use CraigPaul\Moneris\Tests\FeatureTestCase;

#[CoversClass(Receipt::class)]
class ReceiptTest extends FeatureTestCase
{
	#[Test]
	public function serializing_to_json(): void
	{
		$response = $this->gateway()->purchase([
			'order_id' => uniqid('1234-56789', true),
			'amount' => '1.00',
			'credit_card' => $this->visa,
			'expdate' => '2012',
		]);

		$receiptData = json_decode(
			json_encode($response->getReceipt()),
			associative: true
		);

		$this->assertSame($response->getReceipt()->getData(), $receiptData);
	}
}
