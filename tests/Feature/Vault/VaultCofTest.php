<?php

namespace CraigPaul\Moneris\Tests\Feature\Vault;

use CraigPaul\Moneris\Data\CofInfo;
use CraigPaul\Moneris\Vault;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;

#[CoversClass(Vault::class)]
class VaultCofTest extends VaultTestCase
{
	#[Test]
	public function it_adds_a_card_with_cof(): void
	{
		$response = $this->getVault(cof: true)->add($this->card);

		$receipt = $response->getReceipt();

		$this->assertTrue($response->isSuccessful());
		$this->assertNotNull($receipt->read('key'));
	}

	#[Test]
	public function it_updates_a_card_with_cof(): void
	{
		$addResponse = $this->getVault(cof: true)->add($this->card);
		$key = $addResponse->getReceipt()->read('key');

		$this->assertNotNull($key);
		$this->assertTrue($addResponse->isSuccessful());

		// Now we update only the keys we want to be changed. Everything else
		// should remain unchanged in the vault.
		$newEmail = $this->getFaker()->email();

		$updateResponse = $this->getVault()->updatePartial(
			dataKey: $key,
			params: ['email' => $newEmail],
		);

		$this->assertTrue($updateResponse->isSuccessful());
		$this->assertSame($key, $updateResponse->getReceipt()->read('key'));
		$this->assertSame(
			$newEmail,
			$updateResponse->getTransaction()->params['email'],
		);
	}

	#[Test]
	public function it_performs_an_initial_preauth_with_cof(): void
	{
		// First add a card to the vault.

		$addResponse = $this->getVault(cvd: true, cof: true)->add($this->card);
		$dataKey = $addResponse->getReceipt()->read('key');

		$this->assertNotNull($dataKey);
		$this->assertTrue($addResponse->isSuccessful());

		// Then create an initial preauth against the card, using CVD to
		// simulate the most common configuration.

		$preauthParams = [
			'data_key' => $dataKey,
			'order_id' => uniqid('1234-567890', true),
			'amount' => '1.00',
			'cvd' => '123',
			...CofInfo::initial(),
		];

		$preauthResponse = $this->getVault()->preauth($preauthParams);
		$this->assertTrue($preauthResponse->isSuccessful());

		// Then capture the preauth.

		$captureResponse = $this->getVault()->capture(
			$preauthResponse->getTransaction(),
		);

		$this->assertTrue($captureResponse->isSuccessful());

		// Because we're dealing with simulation here, the issuer_id will be
		// null. In production, this value should be filled out.
		$this->assertNull(
			$captureResponse->getTransaction()->params['issuer_id'],
		);
	}
}
