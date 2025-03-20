<?php

namespace CraigPaul\Moneris\Tests\Feature\Validation\Errors;

use CraigPaul\Moneris\Tests\FeatureTestCase;
use CraigPaul\Moneris\Validation\Errors\EmptyError;

#[\PHPUnit\Framework\Attributes\CoversClass(\CraigPaul\Moneris\Validation\Errors\EmptyError::class)]
class EmptyErrorTest extends FeatureTestCase
{
	#[\PHPUnit\Framework\Attributes\Test]
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
