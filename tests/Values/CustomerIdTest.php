<?php

namespace CraigPaul\Moneris\Tests\Values;

use CraigPaul\Moneris\TestSupport\Cases\TestCase;
use CraigPaul\Moneris\Values\CustomerId;
use Iterator;

class CustomerIdTest extends TestCase
{
    public function illegalCharProvider(): Iterator
    {
        yield ['<'];
        yield ['>'];
        yield ['$'];
        yield ['%'];
        yield ['='];
        yield ['?'];
        yield ['^'];
        yield ['{'];
        yield ['}'];
        yield ['['];
        yield [']'];
        yield ['\\'];
    }

    /**
     * @test
     *
     * @dataProvider illegalCharProvider
     */
    public function failing_with_illegal_chars(string $char): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new CustomerId(sprintf('failing%s', $char));
    }

    /** @test */
    public function instantiating(): void
    {
        $value = $this->faker()->slug();
        $customerId = new CustomerId($value);

        $this->assertSame($value, (string) $customerId);
    }
}
