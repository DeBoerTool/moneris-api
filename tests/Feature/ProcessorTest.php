<?php

namespace CraigPaul\Moneris\Tests\Feature;

use CraigPaul\Moneris\Tests\Support\AvsPennyValue;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use CraigPaul\Moneris\Exceptions\InvalidTransactionException;
use CraigPaul\Moneris\Interfaces\GatewayInterface;
use CraigPaul\Moneris\Processor;
use CraigPaul\Moneris\Tests\FeatureTestCase;
use CraigPaul\Moneris\Transaction;
use CraigPaul\Moneris\Values\Crypt;
use GuzzleHttp\Client;

#[CoversClass(Processor::class)]
class ProcessorTest extends FeatureTestCase
{
	protected GatewayInterface $gateway;

	protected Processor $processor;

	protected Transaction $transaction;

	protected array $params;

	public function setUp(): void
	{
		parent::setUp();

		$this->gateway = $this->gateway();

		$this->params = [
			'type' => 'purchase',
			'crypt_type' => Crypt::SSL_ENABLED_MERCHANT,
			'order_id' => uniqid('1234-56789', true),
			'amount' => '1.00',
			'credit_card' => $this->visa,
			'expdate' => '2012',
		];

		$this->transaction = new Transaction($this->gateway, $this->params);
		$this->processor = new Processor(new Client());
	}

	#[Test]
	public function instantiation(): void
	{
		$processor = new Processor(new Client());

		$this->assertInstanceOf(Processor::class, $processor);
	}

	#[Test]
	public function invalid_transaction_data_throws(): void
	{
		$transaction = new Transaction($this->gateway);

		$this->expectException(InvalidTransactionException::class);

		$this->processor->process($transaction);
	}

	#[Test]
	public function submitting_successfully(): void
	{
		$response = $this->processor->process($this->transaction);

		$this->assertTrue($response->isSuccessful());
	}

	#[Test]
	public function it_can_submit_a_avs_secured_request_to_the_moneris_api(): void
	{
		$gateway = $this->gateway(avs: true);
		$response = $gateway->purchase([
			'order_id' => uniqid('1234-56789', true),
			'amount' => AvsPennyValue::approvedFullMatch(),
			'credit_card' => $this->visa,
			'expdate' => '2012',
			'avs_street_number' => '123',
			'avs_street_name' => 'Fake Street',
			'avs_zipcode' => 'X0X0X0',
		]);

		$this->assertTrue($response->isSuccessful());
	}

	#[Test]
	public function it_can_submit_a_cvd_secured_request_to_the_moneris_api(): void
	{
		$gateway = $this->gateway(cvd: true);
		$response = $gateway->purchase([
			'order_id' => uniqid('1234-56789', true),
			'amount' => '1.00',
			'credit_card' => $this->visa,
			'expdate' => '2012',
			'cvd' => '111',
		]);

		$this->assertTrue($response->isSuccessful());
	}
}
