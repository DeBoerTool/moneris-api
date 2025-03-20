<?php

namespace CraigPaul\Moneris\Tests\Feature\Validation\Errors;

use CraigPaul\Moneris\Tests\FeatureTestCase;
use CraigPaul\Moneris\Validation\Errors\UnsupportedTransactionError;

#[\PHPUnit\Framework\Attributes\CoversClass(\CraigPaul\Moneris\Validation\Errors\UnsupportedTransactionError::class)]
class UnsupportedTransactionTest extends FeatureTestCase
{
	#[\PHPUnit\Framework\Attributes\Test]
	public function getting_code_and_message(): void
	{
		$error = new UnsupportedTransactionError();

		$this->assertSame(3, $error->code());
		$this->assertSame(
			'Unsupported transaction type.',
			$error->message()
		);
	}
}
