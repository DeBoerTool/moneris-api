<?php

namespace CraigPaul\Moneris\Tests\Feature;

use CraigPaul\Moneris\Interfaces\GatewayInterface;
use CraigPaul\Moneris\Tests\FeatureTestCase;
use CraigPaul\Moneris\Transaction;
use CraigPaul\Moneris\Validation\Errors\ErrorList;

/**
 * @covers \CraigPaul\Moneris\Transaction
 */
class TransactionTest extends FeatureTestCase
{
    protected GatewayInterface $gateway;

    protected array $params;

    protected Transaction $transaction;

    public function setUp(): void
    {
        parent::setUp();

        $this->gateway = $this->gateway();

        $this->params = [
            'type' => 'purchase',
            'order_id' => uniqid('1234-56789', true),
            'amount' => '1.00',
            'credit_card' => $this->visa,
            'expdate' => '2012',
        ];

        $this->transaction = new Transaction($this->gateway, $this->params);
    }

    /** @test */
    public function getting_class_properties(): void
    {
        $params = $this->params;
        $params['pan'] = $params['credit_card'];
        unset($params['credit_card']);

        $this->assertSame($this->gateway, $this->transaction->gateway);
        $this->assertSame($params, $this->transaction->params);
    }

    /** @test */
    public function getting_the_error_list(): void
    {
        $this->assertInstanceOf(
            ErrorList::class,
            $this->transaction->getErrorList(),
        );

        $this->assertCount(0, $this->transaction->getErrorList());
    }

    /** @test */
    public function getting_the_amount(): void
    {
        $tr = new Transaction($this->gateway(), $this->params);

        $this->assertSame('1.00', $tr->amount());

        $tr = new Transaction($this->gateway(), []);

        $this->assertNull($tr->amount());
    }

    /** @test */
    public function getting_the_transaction_number(): void
    {
        $tr = new Transaction($this->gateway(), []);

        $this->assertNull($tr->number());
    }

    /** @test */
    public function getting_the_order_number(): void
    {
        $tr = new Transaction($this->gateway(), $this->params);

        $this->assertSame($this->params['order_id'], $tr->order());

        $tr = new Transaction($this->gateway(), []);

        $this->assertNull($tr->order());
    }

    /** @test */
    public function formatting_expdate_from_month_and_year(): void
    {
        $params = array_merge($this->params, [
            'expiry_month' => '12',
            'expiry_year' => '20',
        ]);

        unset($params['expdate']);

        $transaction = new Transaction($this->gateway, $params);

        $this->assertSame('2012', $transaction->params['expdate']);
    }

    /** @test */
    public function whitespace_removal(): void
    {
        $transaction = new Transaction($this->gateway, [
            'type' => 'purchase',
            'order_id' => '   1234-567890',
            'amount' => '1.00',
            'credit_card' => '4242 4242 4242 4242',
            'expdate' => '2012',
        ]);

        $this->assertSame(
            '1234-567890',
            $transaction->params['order_id'],
        );

        $this->assertSame(
            '4242424242424242',
            $transaction->params['pan'],
        );
    }

    /** @test */
    public function an_empty_key_is_removed(): void
    {
        $tr = new Transaction($this->gateway(), array_merge($this->params, [
            'key' => '',
        ]));

        $this->assertFalse(isset($tr->params['key']));
    }

    /** @test */
    public function description_key_is_renamed(): void
    {
        $tr = new Transaction($this->gateway(), array_merge($this->params, [
            'description' => 'my description',
        ]));

        $this->assertFalse(isset($tr->params['description']));
        $this->assertSame(
            'my description',
            $tr->params['dynamic_descriptor']
        );
    }

    /** @test */
    public function parameter_validation(): void
    {
        $this->assertTrue($this->transaction->valid());
        $this->assertFalse($this->transaction->invalid());

        $transaction = new Transaction($this->gateway);

        $this->assertFalse($transaction->valid());
        $this->assertTrue($transaction->invalid());
    }

    /** @test */
    public function getting_xml(): void
    {
        $xml = $this->transaction->toXml();
        $xml = simplexml_load_string($xml);

        $this->assertNotEquals(false, $xml);
    }
}
