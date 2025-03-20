<?php

namespace CraigPaul\Moneris\Tests\Feature\Validation\Errors;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use CraigPaul\Moneris\Tests\FeatureTestCase;
use CraigPaul\Moneris\Validation\Errors\UnsupportedTransactionError;

#[CoversClass(UnsupportedTransactionError::class)]
class UnsupportedTransactionTest extends FeatureTestCase
{
	#[Test]
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
