<?php

namespace CraigPaul\Moneris\Tests\Feature\Validation\Errors;

use CraigPaul\Moneris\Tests\TestCase;
use CraigPaul\Moneris\Validation\Errors\EmptyError;

/**
 * @covers \CraigPaul\Moneris\Validation\Errors\EmptyError
 */
class EmptyErrorTest extends TestCase
{
    /** @test */
    public function getting_code_and_message(): void
    {
        $error = new EmptyError();

        $this->assertSame(1, $error->code());
        $this->assertSame(
            'No parameters were provided.',
            $error->message()
        );
    }
}
