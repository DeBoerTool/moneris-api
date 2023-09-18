<?php

namespace CraigPaul\Moneris\Tests\Feature\Traits;

use CraigPaul\Moneris\Tests\TestCase;
use CraigPaul\Moneris\Traits\SettableTrait;
use InvalidArgumentException;

class SettableTest extends TestCase
{
    private object $stub;

    public function setUp(): void
    {
        parent::setUp();

        $this->stub = new class()
        {
            use SettableTrait;

            private mixed $myProp = null;

            public function myProp(): mixed
            {
                return $this->myProp;
            }
        };
    }

    /** @test */
    public function setting_a_property(): void
    {
        $this->assertNull($this->stub->myProp());

        $this->stub->myProp = 'some test value';

        $this->assertSame('some test value', $this->stub->myProp());
    }

    /** @test */
    public function failing_to_set_a_property(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->stub->someNonexistantProperty = [];
    }
}
