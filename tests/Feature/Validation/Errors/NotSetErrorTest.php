<?php

namespace CraigPaul\Moneris\Tests\Feature\Validation\Errors;

use CraigPaul\Moneris\Tests\TestCase;
use CraigPaul\Moneris\Validation\Errors\NotSetError;

/**
 * @covers \CraigPaul\Moneris\Validation\Errors\NotSetError
 */
class NotSetErrorTest extends TestCase
{
    /** @test */
    public function getting_code_and_message(): void
    {
        $error = new NotSetError('my-field');

        $this->assertSame(2, $error->code());
        $this->assertSame(
            'Required field "my-field" not set.',
            $error->message()
        );
        $this->assertSame('my-field', $error->field());
    }
}
