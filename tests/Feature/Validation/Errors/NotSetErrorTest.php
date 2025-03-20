<?php

namespace CraigPaul\Moneris\Tests\Feature\Validation\Errors;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use CraigPaul\Moneris\Tests\FeatureTestCase;
use CraigPaul\Moneris\Validation\Errors\NotSetError;

#[CoversClass(NotSetError::class)]
class NotSetErrorTest extends FeatureTestCase
{
	#[Test]
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
