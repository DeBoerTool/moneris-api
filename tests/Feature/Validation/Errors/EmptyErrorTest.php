<?php

namespace CraigPaul\Moneris\Tests\Feature\Validation\Errors;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use CraigPaul\Moneris\Tests\FeatureTestCase;
use CraigPaul\Moneris\Validation\Errors\EmptyError;

#[CoversClass(EmptyError::class)]
class EmptyErrorTest extends FeatureTestCase
{
	#[Test]
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
