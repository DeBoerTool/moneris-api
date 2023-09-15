<?php

namespace CraigPaul\Moneris\Tests\Feature;

use CraigPaul\Moneris\Customer;
use CraigPaul\Moneris\Tests\TestCase;
use InvalidArgumentException;

/**
 * @covers \CraigPaul\Moneris\Customer
 */
class CustomerTest extends TestCase
{
    protected array $rawData = [
        'id' => null,
        'email' => null,
        'phone' => null,
        'note' => null,
    ];

    /** @test */
    public function instantiation(): void
    {
        $customer = new Customer();

        $this->assertInstanceOf(Customer::class, $customer);
        $this->assertSame($this->rawData, $customer->data);
    }

    /** @test */
    public function static_constructor(): void
    {
        $customer = Customer::create();

        $this->assertInstanceOf(Customer::class, $customer);
        $this->assertSame($this->rawData, $customer->data);
    }

    /** @test */
    public function getting_customer_data(): void
    {
        $params = [
            'id' => uniqid('customer-', true),
            'email' => 'example@email.com',
            'phone' => '555-555-5555',
            'note' => 'Customer note',
        ];

        $customer = Customer::create($params);

        array_walk(
            $params,
            fn ($v, $k) => $this->assertSame($params[$k], $customer->{$k})
        );

        $this->assertSame($params, $customer->data);
    }

    /** @test */
    public function failing_to_get_customer_data(): void
    {
        $customer = Customer::create();

        $this->expectException(InvalidArgumentException::class);

        /** @noinspection PhpUndefinedFieldInspection */
        /** @noinspection PhpExpressionResultUnusedInspection */
        $customer->nonexistantProperty;
    }

    /** @test */
    public function setting_data(): void
    {
        $customer = Customer::create();

        $this->assertSame($this->rawData, $customer->data);

        $newData = ['new', 'data'];
        $customer->data = $newData;

        $this->assertSame($newData, $customer->data);

        $customer->foo = 'bar';

        $this->assertSame('bar', $customer->data['foo']);
    }
}
