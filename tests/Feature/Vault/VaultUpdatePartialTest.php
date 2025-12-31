<?php

namespace CraigPaul\Moneris\Tests\Feature\Vault;

use CraigPaul\Moneris\Customer;
use CraigPaul\Moneris\Enums\CryptType;
use CraigPaul\Moneris\Enums\TransactionType;
use CraigPaul\Moneris\Transaction;
use CraigPaul\Moneris\Vault;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;

#[CoversClass(Vault::class)]
class VaultUpdatePartialTest extends VaultTestCase
{
	protected function assertHasMatchingData(
		Transaction $transaction,
		bool $isUpdate = false,
		array $updates = [],
	): void {
		// The data returned from Moneris as part of the transaction.
		$data = $transaction->params;

		$this->assertSame(
			$isUpdate
				? TransactionType::VaultCardUpdate->value
				: TransactionType::VaultCardAdd->value,
			$data['type'],
		);

		if (isset($data['crypt_type'])) {
			$this->assertSame(
				CryptType::SslEnabledMerchant->value,
				$data['crypt_type']->value(),
			);
		}

		// The possible keys that can be updated and their sources.
		$keys = [
			'pan' => $this->card->number,
			'expdate' => $this->card->expiry,
			'cust_id' => $this->card->customer->id,
			'phone' => $this->card->customer->phone,
			'email' => $this->card->customer->email,
			'note' => $this->card->customer->note,
		];

		foreach ($keys as $key => $expected) {
			if (!isset($data[$key])) {
				continue;
			}

			$this->assertSame($updates[$key] ?? $expected, $data[$key]);
		}
	}

	#[Test]
	public function it_can_update_a_card_with_partial_params(): void
	{
		$params = [
			'id' => uniqid('customer-', true),
			'email' => $this->getFaker()->email(),
			'phone' => $this->getFaker()->phoneNumber(),
			'note' => 'A 30-char max note.',
		];

		$card = $this->card->attach(Customer::create($params));

		$addResponse = $this->getVault()->add($card);
		$key = $addResponse->getReceipt()->read('key');

		$this->assertNotNull($key);
		$this->assertTrue($addResponse->isSuccessful());
		$this->assertHasMatchingData($addResponse->getTransaction());

		// Now we update only the keys we want to be changed. Everything else
		// should remain unchanged in the vault.
		$newEmail = $this->getFaker()->email();

		$updateResponse = $this->getVault()->updatePartial(
			dataKey: $key,
			params: ['email' => $newEmail],
		);

		$this->assertTrue($updateResponse->isSuccessful());
		$this->assertSame($key, $updateResponse->getReceipt()->read('key'));
		$this->assertHasMatchingData(
			transaction: $updateResponse->getTransaction(),
			isUpdate: true,
			updates: ['email' => $newEmail],
		);
	}
}
