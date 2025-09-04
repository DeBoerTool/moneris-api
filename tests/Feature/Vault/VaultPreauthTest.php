<?php

namespace CraigPaul\Moneris\Tests\Feature\Vault;

use CraigPaul\Moneris\Enums\ResponseErrorEnum;
use CraigPaul\Moneris\Tests\Support\AvsPennyValue;
use CraigPaul\Moneris\Vault;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;

#[CoversClass(Vault::class)]
class VaultPreauthTest extends VaultTestCase
{
	#[Test]
	public function it_vault_preauths(): void
	{
		$response = $this->getVault()->add($this->card);
		$key = $response->getReceipt()->read('key');

		$params = array_merge($this->params, ['data_key' => $key]);

		$response = $this->getVault()->preauth($params);
		$receipt = $response->getReceipt();

		$this->assertTrue($response->isSuccessful());
		$this->assertEquals($key, $receipt->read('key'));
		$this->assertTrue($receipt->read('complete'));
	}

	#[Test]
	public function it_fails_vault_preauth_with_declined(): void
	{
		$response = $this->getVault()->add($this->card);
		$key = $response->getReceipt()->read('key');

		$params = array_merge($this->params, [
			'data_key' => $key,
			'amount' => '0.05',
		]);

		$response = $this->getVault()->preauth($params);

		$this->assertFalse($response->isSuccessful());
		$this->assertSame(ResponseErrorEnum::Declined, $response->getError());
	}

	#[Test]
	public function it_vault_preauths_with_customer_info(): void
	{
		$response = $this->getVault()->add($this->card);
		$key = $response->getReceipt()->read('key');

		$params = array_merge($this->params, [
			'data_key' => $key,
			'cust_id' => uniqid('customer-', true),
			'cust_info' => $this->customer,
		]);

		$response = $this->getVault()->preauth($params);
		$receipt = $response->getReceipt();

		$this->assertTrue($response->isSuccessful());
		$this->assertEquals($key, $receipt->read('key'));
		$this->assertTrue($receipt->read('complete'));
	}

	#[Test]
	public function it_vault_preauths_with_cvd(): void
	{
		$vault = $this->gateway(cvd: true)->cards();

		$response = $this->getVault()->add($this->card);
		$key = $response->getReceipt()->read('key');

		$params = array_merge($this->params, [
			'data_key' => $key,
			'cvd' => '111',
		]);

		$response = $vault->preauth($params);
		$receipt = $response->getReceipt();

		$this->assertTrue($response->isSuccessful());
		$this->assertEquals($key, $receipt->read('key'));
		$this->assertTrue($receipt->read('complete'));
	}

	#[Test]
	public function it_vault_preauths_with_avs(
	) {
		$vault = $this->gateway(avs: true)->cards();

		$response = $this->getVault()->add($this->card);
		$key = $response->getReceipt()->read('key');

		$params = array_merge($this->params, [
			'data_key' => $key,
			'avs_street_number' => '123',
			'avs_street_name' => 'Fake Street',
			'avs_zipcode' => 'X0X0X0',
			'amount' => AvsPennyValue::approvedFullMatch(),
		]);

		$response = $vault->preauth($params);
		$receipt = $response->getReceipt();

		$this->assertTrue($response->isSuccessful());
		$this->assertEquals($key, $receipt->read('key'));
		$this->assertTrue($receipt->read('complete'));
	}
}
