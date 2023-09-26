<?php

namespace CraigPaul\Moneris\Tests\Feature;

use CraigPaul\Moneris\TestSupport\Cases\TestCase;
use CraigPaul\Moneris\Transaction;
use CraigPaul\Moneris\Validation\Errors\ErrorList;

/**
 * @covers \CraigPaul\Moneris\Transaction
 */
class TransactionTest extends TestCase
{
    protected function params(): array
    {
        return [
            'type' => 'purchase',
            'order_id' => uniqid('1234-56789', true),
            'amount' => '1.00',
            'credit_card' => $this->visa,
            'expdate' => '2012',
        ];
    }

    protected function transaction(array|null $params = null): Transaction
    {
        $params = is_null($params)
            ? $this->params()
            : $params;

        return new Transaction($this->gateway()->getConfig(), $params);
    }

    /** @test */
    public function getting_the_error_list(): void
    {
        $this->assertInstanceOf(
            ErrorList::class,
            $this->transaction()->getErrorList(),
        );

        $this->assertCount(0, $this->transaction()->getErrorList());
    }

    /** @test */
    public function getting_the_amount(): void
    {
        $this->assertSame('1.00', $this->transaction()->amount());
        $this->assertNull($this->transaction([])->amount());
    }

    /** @test */
    public function getting_the_transaction_number(): void
    {
        $this->assertNull($this->transaction()->number());
    }

    /** @test */
    public function getting_the_order_number(): void
    {
        $params = $this->params();

        $this->assertSame(
            $params['order_id'],
            $this->transaction($params)->order()
        );

        $this->assertNull($this->transaction([])->order());
    }

    /** @test */
    public function formatting_expdate_from_month_and_year(): void
    {
        $params = array_merge($this->params(), [
            'expiry_month' => '12',
            'expiry_year' => '20',
        ]);

        unset($params['expdate']);

        $this->assertSame(
            '2012',
            $this->transaction($params)->params['expdate'],
        );
    }

    /** @test */
    public function whitespace_removal(): void
    {
        $transaction = new Transaction($this->gateway()->getConfig(), [
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
    public function empty_key_removal(): void
    {
        $transaction = new Transaction(
            $this->gateway()->getConfig(),
            array_merge($this->params(), ['key' => '']),
        );

        $this->assertFalse(isset($transaction->params['key']));
    }

    /** @test */
    public function description_key_is_renamed(): void
    {
        $transaction = new Transaction(
            $this->gateway()->getConfig(),
            array_merge($this->params(), ['description' => 'my description']),
        );

        $this->assertFalse(isset($transaction->params['description']));
        $this->assertSame(
            'my description',
            $transaction->params['dynamic_descriptor'],
        );
    }

    /** @test */
    public function parameter_validation(): void
    {
        $this->assertTrue($this->transaction()->valid());
        $this->assertFalse($this->transaction()->invalid());

        $transaction = new Transaction($this->gateway()->getConfig());

        $this->assertFalse($transaction->valid());
        $this->assertTrue($transaction->invalid());
    }

    /** @test */
    public function getting_xml(): void
    {
        $xml = $this->transaction()->toXml();
        $xml = simplexml_load_string($xml);

        $this->assertNotEquals(false, $xml);
    }
}
