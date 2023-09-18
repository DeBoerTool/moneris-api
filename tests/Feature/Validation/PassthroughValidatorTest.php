<?php

namespace CraigPaul\Moneris\Tests\Feature\Validation;

use CraigPaul\Moneris\Tests\TestCase;
use CraigPaul\Moneris\Validation\PassthroughValidator;

/**
 * @covers \CraigPaul\Moneris\Validation\PassthroughValidator
 */
class PassthroughValidatorTest extends TestCase
{
    /** @test */
    public function passing_and_getting_error(): void
    {
        $val = new PassthroughValidator();

        $this->assertTrue($val->passes());
        $this->assertCount(0, $val->errors());
    }
}
