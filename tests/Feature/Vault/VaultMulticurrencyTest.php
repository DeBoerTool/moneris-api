<?php

namespace CraigPaul\Moneris\Tests\Feature\Vault;

use CraigPaul\Moneris\Enums\Currency;
use CraigPaul\Moneris\Vault;
use CraigPaul\Moneris\Vault\Request\McpVaultPreauthRequest;
use CraigPaul\Moneris\Vault\Request\McpVaultPurchaseRequest;
use PHPUnit\Framework\Attributes\CoversClass;
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

		$response = $this->vault->mcpPreauth($data);
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
			amount: $this->params['amount'],
			currency: Currency::USD,
		);

		$preauthResponse = $this->vault->mcpPreauth($data);

		$this->assertTrue($preauthResponse->isSuccessful());

		$captureResponse = $this->vault->capture(
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

		$response = $this->vault->mcpPurchase($data);
		$receipt = $response->getReceipt();

		$this->assertTrue($response->isSuccessful());
		$this->assertEquals((string) $dataKey, $receipt->read('key'));
		$this->assertTrue($receipt->read('complete'));
	}
}
