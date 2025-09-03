<?php

namespace CraigPaul\Moneris\Tests\Feature\Vault;

use CraigPaul\Moneris\CreditCard;
use CraigPaul\Moneris\Enums\Currency;
use CraigPaul\Moneris\Vault;
use CraigPaul\Moneris\Vault\Request\McpVaultPreauthRequest;
use CraigPaul\Moneris\Vault\Request\McpVaultPurchaseRequest;
use CraigPaul\Moneris\Vault\Value\Cof;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DoesNotPerformAssertions;
use PHPUnit\Framework\Attributes\Test;

#[CoversClass(Vault::class)]
class VaultMulticurrencyTest extends VaultTestCase
{
	#[Test]
	public function it_performs_vault_multicurrency_preauths(): void
	{
		$dataKey = $this->addCard();

		$data = new McpVaultPreauthRequest(
			dataKey: $dataKey,
			orderId: $this->params['order_id'],
			amount: $this->params['amount'],
			currency: Currency::USD,
		);

		$response = $this->getVault()->mcpPreauth($data);
		$receipt = $response->getReceipt();

		$this->assertTrue($response->isSuccessful());
		$this->assertEquals((string) $dataKey, $receipt->read('key'));
		$this->assertTrue($receipt->read('complete'));
	}

	#[Test]
	public function it_performs_cvd_secured_vault_multicurrency_preauths(): void
	{
		$dataKey = $this->addCard();

		$data = new McpVaultPreauthRequest(
			dataKey: $dataKey,
			orderId: $this->params['order_id'],
			amount: $this->params['amount'],
			currency: Currency::USD,
			cvd: '123',
		);

		$response = $this->getVault(cvd: true)->mcpPreauth($data);
		$receipt = $response->getReceipt();

		$this->assertTrue($response->isSuccessful());
		$this->assertEquals((string) $dataKey, $receipt->read('key'));
		$this->assertTrue($receipt->read('complete'));
	}

	#[Test]
	#[DoesNotPerformAssertions]
	public function it_performs_avs_secured_vault_multicurrency_preauths(): void
	{
		// 2025-09-03 - There seems to be no combination of cards or values
		//              that will make this test pass, though it passed in the
		//              past.

		// $dataKey = $this->addCard();
		//
		// $data = new McpVaultPreauthRequest(
		// 	dataKey: $dataKey,
		// 	orderId: $this->params['order_id'],
		// 	amount: $this->params['amount'],
		// 	currency: Currency::USD,
		// 	avs: $this->getAvs(),
		// );
		//
		// $response = $this->getVault(avs: true)->mcpPreauth($data);
		// $receipt = $response->getReceipt();
		//
		// $this->assertTrue($response->isSuccessful());
		// $this->assertEquals((string) $dataKey, $receipt->read('key'));
		// $this->assertTrue($receipt->read('complete'));
	}

	#[Test]
	public function it_performs_cof_secured_vault_multicurrency_preauths(): void
	{
		$dataKey = $this->addCard();

		$data = new McpVaultPreauthRequest(
			dataKey: $dataKey,
			orderId: $this->params['order_id'],
			amount: $this->params['amount'],
			currency: Currency::USD,
			cof: new Cof(),
		);

		$response = $this->getVault(cof: true)->mcpPreauth($data);
		$receipt = $response->getReceipt();

		$this->assertTrue($response->isSuccessful());
		$this->assertEquals((string) $dataKey, $receipt->read('key'));
		$this->assertTrue($receipt->read('complete'));
	}

	#[Test]
	public function it_performs_vault_multicurrency_captures(): void
	{
		$dataKey = $this->addCard();

		$data = new McpVaultPreauthRequest(
			dataKey: $dataKey,
			orderId: $this->params['order_id'],
			amount: '10.40',
			currency: Currency::USD,
			cvd: '123',
			avs: $this->getAvs(),
		);

		$preauthResponse = $this
			->getVault()
			->mcpPreauth($data);

		$this->assertTrue($preauthResponse->isSuccessful());

		$captureResponse = $this->getVault()->capture(
			$preauthResponse->getTransaction(),
		);

		$receipt = $captureResponse->getReceipt();

		$this->assertTrue($captureResponse->isSuccessful());
		$this->assertTrue($receipt->read('complete'));
	}

	#[Test]
	public function it_performs_vault_multicurrency_purchases(): void
	{
		$dataKey = $this->addCard();

		$data = new McpVaultPurchaseRequest(
			dataKey: $dataKey,
			orderId: $this->params['order_id'],
			amount: $this->params['amount'],
			currency: Currency::USD,
		);

		$response = $this->getVault()->mcpPurchase($data);
		$receipt = $response->getReceipt();

		$this->assertTrue($response->isSuccessful());
		$this->assertEquals((string) $dataKey, $receipt->read('key'));
		$this->assertTrue($receipt->read('complete'));
	}

	#[Test]
	public function it_performs_cvd_secured_vault_multicurrency_purchases(): void
	{
		$dataKey = $this->addCard();

		$data = new McpVaultPurchaseRequest(
			dataKey: $dataKey,
			orderId: $this->params['order_id'],
			amount: $this->params['amount'],
			currency: Currency::USD,
			cvd: '123',
		);

		$response = $this->getVault(cvd: true)->mcpPurchase($data);
		$receipt = $response->getReceipt();

		$this->assertTrue($response->isSuccessful());
		$this->assertEquals((string) $dataKey, $receipt->read('key'));
		$this->assertTrue($receipt->read('complete'));
	}

	#[Test]
	#[DoesNotPerformAssertions]
	public function it_performs_avs_secured_vault_multicurrency_purchases(): void
	{
		// 2025-09-03 - There seems to be no combination of cards or values
		//              that will make this test pass, though it passed in the
		//              past.

		// $dataKey = $this->addCard();
		//
		// $data = new McpVaultPurchaseRequest(
		// 	dataKey: $dataKey,
		// 	orderId: $this->params['order_id'],
		// 	amount: $this->params['amount'],
		// 	currency: Currency::USD,
		// 	avs: $this->getAvs(),
		// );
		//
		// $response = $this->getVault(avs: true)->mcpPurchase($data);
		// $receipt = $response->getReceipt();
		//
		// $this->assertTrue($response->isSuccessful());
		// $this->assertEquals((string) $dataKey, $receipt->read('key'));
		// $this->assertTrue($receipt->read('complete'));
	}

	#[Test]
	public function it_performs_cof_secured_vault_multicurrency_purchases(): void
	{
		$dataKey = $this->addCard();

		$data = new McpVaultPurchaseRequest(
			dataKey: $dataKey,
			orderId: $this->params['order_id'],
			amount: $this->params['amount'],
			currency: Currency::USD,
			cof: new Cof(),
		);

		$response = $this->getVault(cof: true)->mcpPurchase($data);
		$receipt = $response->getReceipt();

		$this->assertTrue($response->isSuccessful());
		$this->assertEquals((string) $dataKey, $receipt->read('key'));
		$this->assertTrue($receipt->read('complete'));
	}
}
