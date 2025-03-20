<?php

namespace CraigPaul\Moneris\Tests\Feature\Validation;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use CraigPaul\Moneris\Tests\FeatureTestCase;
use CraigPaul\Moneris\Validation\PassthroughValidator;

#[CoversClass(PassthroughValidator::class)]
class PassthroughValidatorTest extends FeatureTestCase
{
	#[Test]
	public function passing_and_getting_error(): void
	{
		$val = new PassthroughValidator();

		$this->assertTrue($val->passes());
		$this->assertCount(0, $val->errors());
	}
}
