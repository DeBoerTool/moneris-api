<?php

namespace CraigPaul\Moneris\Tests\Feature\Vault;

use CraigPaul\Moneris\CreditCard;
use CraigPaul\Moneris\Tests\FeatureTestCase;
use CraigPaul\Moneris\Vault;
use CraigPaul\Moneris\Vault\Value\Avs;
use CraigPaul\Moneris\Vault\Value\DataKey;
use Faker\Factory as Faker;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Vault::class)]
abstract class VaultTestCase extends FeatureTestCase
{
	protected array $billing;

	protected CreditCard $card;

	protected array $customer;

	protected array $items;

	protected array $params;

	protected Vault|null $vault = null;

	public function setUp(): void
	{
		parent::setUp();

		$faker = Faker::create();
		$this->card = CreditCard::create($this->visa, '2012');
		$this->params = [
			'order_id' => uniqid('1234-567890', true),
			'amount' => '1.00',
		];
		$this->billing = [
			'first_name' => $faker->firstName,
			'last_name' => $faker->lastName,
			'company_name' => $faker->company,
			'address' => $faker->streetAddress,
			'city' => $faker->city,
			'province' => 'SK',
			'postal_code' => 'X0X0X0',
			'country' => 'Canada',
			'phone_number' => '555-555-5555',
			'fax' => '555-555-5555',
			'tax1' => '1.01',
			'tax2' => '1.02',
			'tax3' => '1.03',
			'shipping_cost' => '9.99',
		];
		$this->items = [
			[
				'name' => $faker->sentence(mt_rand(3, 6)),
				'quantity' => '1',
				'product_code' => $faker->isbn10,
				'extended_amount' => $faker->randomFloat(2, 0.01, 999.99),
			],
			[
				'name' => $faker->sentence(mt_rand(3, 6)),
				'quantity' => '1',
				'product_code' => $faker->isbn10,
				'extended_amount' => $faker->randomFloat(2, 0.01, 999.99),
			],
		];
		$this->customer = [
			'email' => 'example@email.com',
			'instructions' => $faker->sentence(mt_rand(3, 6)),
			'billing' => $this->billing,
			'shipping' => $this->billing,
			'items' => $this->items,
		];
	}

	protected function addCard(): DataKey
	{
		$response = $this->gateway()->cards()->add($this->card);

		return new DataKey($response->getReceipt()->read('key'));
	}

	protected function getVault(
		bool $avs = false,
		bool $cvd = false,
		bool $cof = false,
	): Vault {
		if ($this->vault === null) {
			$this->vault = $this
				->gateway(avs: $avs, cvd: $cvd, cof: $cof)
				->vault();
		}

		return $this->vault;
	}

	protected function getAvs(): Avs
	{
		return new Avs(
			streetNumber: '123',
			streetName: 'Fake Street',
			postalCode: 'X0X0X0',
		);
	}
}
