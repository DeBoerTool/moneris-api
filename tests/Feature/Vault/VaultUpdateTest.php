<?php

namespace CraigPaul\Moneris\Tests\Feature\Vault;

use CraigPaul\Moneris\Customer;
use CraigPaul\Moneris\Vault;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;

use function mock_handler;

#[CoversClass(Vault::class)]
class VaultUpdateTest extends VaultTestCase
{
	#[Test]
	public function it_can_update_a_card(): void
	{
		$response = $this->getVault()->add($this->card);
		$key = $response->getReceipt()->read('key');

		$this->assertSame(
			'2012',
			$response->getTransaction()->params['expdate'],
		);

		$this->card->expiry = '2112';

		$response = $this->getVault()->update($this->card, $key);

		$this->assertTrue($response->isSuccessful());
		$this->assertNotNull($response->getReceipt()->read('key'));
		$this->assertSame($key, $response->getReceipt()->read('key'));
		$this->assertSame(
			'2112',
			$response->getTransaction()->params['expdate'],
		);
	}

	#[Test]
	public function it_can_update_a_card_with_customer_data()
	{
		$params = [
			'id' => uniqid('customer-', true),
			'email' => 'example@email.com',
			'phone' => '555-555-5555',
			'note' => 'Customer note',
		];
		$customer = Customer::create($params);
		$card = $this->card->attach($customer);

		$response = $this->getVault()->add($card);
		$key = $response->getReceipt()->read('key');

		$this->card->customer->email = 'example2@email.com';

		$response = $this->getVault()->update($this->card, $key);
		$receipt = $response->getReceipt();

		$this->assertTrue($response->isSuccessful());
		$this->assertNotNull($receipt->read('key'));
		$this->assertEquals($key, $receipt->read('key'));
		$this->assertEquals('example2@email.com', $receipt->read('data')['email']);
	}
}
