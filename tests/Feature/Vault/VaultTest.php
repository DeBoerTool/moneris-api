<?php

namespace CraigPaul\Moneris\Tests\Feature\Vault;

use CraigPaul\Moneris\CreditCard;
use CraigPaul\Moneris\Customer;
use CraigPaul\Moneris\Processor;
use CraigPaul\Moneris\Receipt;
use CraigPaul\Moneris\Response;
use CraigPaul\Moneris\Tests\Support\AvsPennyValue;
use CraigPaul\Moneris\Tests\Support\Stubs\VaultExpiringStub;
use CraigPaul\Moneris\Transaction;
use CraigPaul\Moneris\Vault;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;

use function mock_handler;

#[CoversClass(Vault::class)]
class VaultTest extends VaultTestCase
{
	#[Test]
	public function instantiating(): void
	{
		$vault = new Vault($this->id, $this->token, $this->environment);

		$this->assertEquals(Vault::class, $vault::class);
		$this->assertObjectHasProperty('id', $vault);
		$this->assertObjectHasProperty('token', $vault);
		$this->assertObjectHasProperty('environment', $vault);
	}

	#[Test]
	public function static_constructor(): void
	{
		$vault = Vault::create($this->id, $this->token, $this->environment);

		$this->assertEquals(Vault::class, $vault::class);
		$this->assertObjectHasProperty('id', $vault);
		$this->assertObjectHasProperty('token', $vault);
		$this->assertObjectHasProperty('environment', $vault);
	}

	#[Test]
	public function adding_a_credit_card_and_getting_a_data_key(): void
	{
		$response = $this->getVault()->add($this->card);
		$receipt = $response->getReceipt();

		$this->assertTrue($response->isSuccessful());
		$this->assertNotNull($receipt->read('key'));
	}

	#[Test]
	public function it_can_add_a_credit_card_with_an_attached_customer_to_the_moneris_vault_and_returns_a_data_key_for_storage(
	) {
		$params = [
			'id' => uniqid('customer-', true),
			'email' => 'example@email.com',
			'phone' => '555-555-5555',
			'note' => 'Customer note',
		];
		$customer = Customer::create($params);
		$card = $this->card->attach($customer);

		$response = $this->getVault()->add($card);
		$receipt = $response->getReceipt();

		$this->assertTrue($response->isSuccessful());
		$this->assertNotNull($receipt->read('key'));
		$this->assertEquals($params['id'], $receipt->read('data')['customer_id']);
		$this->assertEquals($params['phone'], $receipt->read('data')['phone']);
		$this->assertEquals($params['email'], $receipt->read('data')['email']);
		$this->assertEquals($params['note'], $receipt->read('data')['note']);
	}

	#[Test]
	public function updating_a_card_and_getting_the_provided_data_key(): void
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
	public function it_can_update_a_credit_card_with_an_attached_customer_to_the_moneris_vault_and_returns_a_data_key_for_storage(
	) {
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

	#[Test]
	public function it_can_delete_a_credit_card_from_the_moneris_vault_and_returns_a_data_key_for_storage()
	{
		$response = $this->getVault()->add($this->card);
		$key = $response->getReceipt()->read('key');

		$response = $this->getVault()->delete($key);
		$receipt = $response->getReceipt();

		$this->assertTrue($response->isSuccessful());
		$this->assertNotNull($receipt->read('key'));
		$this->assertEquals($key, $receipt->read('key'));
	}

	#[Test]
	public function it_can_tokenize_a_previous_transaction_to_add_the_transactions_credit_card_in_the_moneris_vault_and_returns_a_data_key_for_storage(
	) {
		$gateway = $this->gateway();

		$response = $gateway->purchase([
			'order_id' => uniqid('1234-56789', true),
			'amount' => '1.00',
			'credit_card' => $this->visa,
			'expdate' => '2012',
		]);

		$response = $this->getVault()->tokenize($response->getTransaction());
		$receipt = $response->getReceipt();

		$this->assertTrue($response->isSuccessful());
		$this->assertNotNull($receipt->read('key'));
	}

	#[Test]
	public function it_can_peek_into_the_vault_and_retrieve_a_masked_credit_card_from_the_moneris_vault_with_a_valid_data_key(
	) {
		$response = $this->getVault()->add($this->card);
		$key = $response->getReceipt()->read('key');

		$response = $this->getVault()->peek($key);
		$receipt = $response->getReceipt();
		$beginning = substr($this->visa, 0, 4);
		$end = substr($this->visa, -4, 4);

		$this->assertTrue($response->isSuccessful());
		$this->assertNotNull($receipt->read('key'));
		$this->assertEquals('12', $receipt->read('data')['expiry_date']['month']);
		$this->assertEquals('20', $receipt->read('data')['expiry_date']['year']);
		$this->assertEquals($beginning, substr((string) $receipt->read('data')['masked_pan'], 0, 4));
		$this->assertEquals($end, substr((string) $receipt->read('data')['masked_pan'], -4, 4));
	}

	#[Test]
	public function it_can_retrieve_all_expiring_credit_cards_from_the_moneris_vault()
	{
		$expiry = date('ym', strtotime('today + 10 days'));
		$cardAddResponses = [];

		$card = CreditCard::create($this->visa, $expiry);
		$cardAddResponses[] = $this->getVault()->add($card);
		$card = CreditCard::create($this->mastercard, $expiry);
		$cardAddResponses[] = $this->getVault()->add($card);
		$card = CreditCard::create($this->amex, $expiry);
		$cardAddResponses[] = $this->getVault()->add($card);

		$client = mock_handler(
			(new VaultExpiringStub())->render($cardAddResponses),
		);

		$params = ['type' => 'res_get_expiring'];
		$transaction = new Transaction($this->getVault(), $params);
		$this->getVault()->transaction = $transaction;
		$processor = new Processor($client);

		$response = $processor->process($transaction);
		$receipt = $response->getReceipt();

		$this->assertTrue($response->isSuccessful());
		$this->assertGreaterThan(0, count($receipt->read('data')));

		/** @var Response $card */
		foreach ($cardAddResponses as $index => $card) {
			/** @var Receipt $rec */
			$rec = $card->getReceipt();

			$this->assertEquals($rec->read('key'), $receipt->read('data')[$index]['data_key']);
			$this->assertEquals($rec->read('data')['masked_pan'], $receipt->read('data')[$index]['masked_pan']);
		}
	}

	#[Test]
	public function it_can_make_a_purchase_with_a_credit_card_stored_in_the_moneris_vault()
	{
		$response = $this->getVault()->add($this->card);
		$key = $response->getReceipt()->read('key');

		$params = array_merge($this->params, [
			'data_key' => $key,
		]);

		$response = $this->getVault()->purchase($params);
		$receipt = $response->getReceipt();

		$this->assertTrue($response->isSuccessful());
		$this->assertEquals($key, $receipt->read('key'));
		$this->assertEquals(true, $receipt->read('complete'));
	}

	#[Test]
	public function it_can_make_a_purchase_with_a_credit_card_stored_in_the_moneris_vault_and_attach_customer_info()
	{
		$response = $this->getVault()->add($this->card);
		$key = $response->getReceipt()->read('key');

		$params = array_merge($this->params, [
			'data_key' => $key,
			'cust_id' => uniqid('customer-', true),
			'cust_info' => $this->customer,
		]);

		$response = $this->getVault()->purchase($params);
		$receipt = $response->getReceipt();

		$this->assertTrue($response->isSuccessful());
		$this->assertEquals($key, $receipt->read('key'));
		$this->assertEquals(true, $receipt->read('complete'));
	}

	#[Test]
	public function it_can_submit_a_cvd_secured_purchase_with_a_credit_card_stored_in_the_moneris_vault()
	{
		$vault = $this->gateway(cvd: true)->cards();

		$response = $this->getVault()->add($this->card);
		$key = $response->getReceipt()->read('key');

		$params = array_merge($this->params, [
			'data_key' => $key,
			'cvd' => '111',
		]);

		$response = $vault->purchase($params);
		$receipt = $response->getReceipt();

		$this->assertTrue($response->isSuccessful());
		$this->assertEquals($key, $receipt->read('key'));
		$this->assertEquals(true, $receipt->read('complete'));
	}

	#[Test]
	public function it_can_submit_an_avs_secured_purchase_with_a_credit_card_stored_in_the_moneris_vault()
	{
		$vault = $this->gateway(avs: true)->cards();

		$response = $this->getVault()->add($this->card);
		$key = $response->getReceipt()->read('key');

		$params = array_merge($this->params, [
			'data_key' => $key,
			'avs_street_number' => '123',
			'avs_street_name' => 'Fake Street',
			'avs_zipcode' => 'X0X0X0',
		]);

		$response = $vault->purchase($params);
		$receipt = $response->getReceipt();

		$this->assertTrue($response->isSuccessful());
		$this->assertEquals($key, $receipt->read('key'));
		$this->assertEquals(true, $receipt->read('complete'));
	}

	#[Test]
	public function it_can_preauth_against_a_vault_card()
	{
		$response = $this->getVault()->add($this->card);
		$key = $response->getReceipt()->read('key');

		$params = array_merge($this->params, [
			'data_key' => $key,
		]);

		$response = $this->getVault()->preauth($params);
		$receipt = $response->getReceipt();

		$this->assertTrue($response->isSuccessful());
		$this->assertEquals($key, $receipt->read('key'));
		$this->assertTrue($receipt->read('complete'));
	}

	#[Test]
	public function it_can_pre_authorize_a_credit_card_stored_in_the_moneris_vault_and_attach_customer_info()
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
		$this->assertEquals(true, $receipt->read('complete'));
	}

	#[Test]
	public function it_can_submit_a_cvd_secured_pre_authorization_request_for_a_credit_card_stored_in_the_moneris_vault(
	) {
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
		$this->assertEquals(true, $receipt->read('complete'));
	}

	#[Test]
	public function it_can_submit_an_avs_secured_pre_authorization_request_for_a_credit_card_stored_in_the_moneris_vault(
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

	#[Test]
	public function it_captures_preauths_against_vault_cards()
	{
		$response = $this->getVault()->add($this->card);
		$key = $response->getReceipt()->read('key');

		$params = array_merge($this->params, [
			'data_key' => $key,
		]);

		$response = $this->getVault()->preauth($params);
		$response = $this->getVault()->capture($response->getTransaction());
		$receipt = $response->getReceipt();

		$this->assertTrue($response->isSuccessful());
		$this->assertTrue($receipt->read('complete'));
	}

	#[Test]
	public function it_makes_purchases_against_vault_cards()
	{
		$gateway = $this->gateway(cof: true);
		$vault = $gateway->cards();

		$preauth_params = [
			'order_id' => uniqid('1234-56789', true),
			'amount' => '1.00',
			'credit_card' => $this->visa,
			'expdate' => '2012',
			'payment_indicator' => 'C',
			'payment_information' => '0',
		];

		$response = $gateway->preauth($preauth_params);
		$issuer_id = $response->getReceipt()->read('issuer_id');
		$response = $vault->add($this->card, ['issuer_id' => $issuer_id]);
		$key = $response->getReceipt()->read('key');

		$params = array_merge($this->params, [
			'data_key' => $key,
			'payment_indicator' => 'U',
			'payment_information' => '2',
			'issuer_id' => $issuer_id,
		]);

		$response = $vault->purchase($params);
		$receipt = $response->getReceipt();

		$this->assertTrue($response->isSuccessful());
		$this->assertSame($key, $receipt->read('key'));
		$this->assertTrue($receipt->read('complete'));
		$this->assertNotEmpty($receipt->read('issuer_id'));
	}
}
