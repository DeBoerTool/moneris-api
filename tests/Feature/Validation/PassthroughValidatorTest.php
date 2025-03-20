<?php

namespace CraigPaul\Moneris\Tests\Feature\Validation;

use CraigPaul\Moneris\Tests\FeatureTestCase;
use CraigPaul\Moneris\Validation\PassthroughValidator;

#[\PHPUnit\Framework\Attributes\CoversClass(\CraigPaul\Moneris\Validation\PassthroughValidator::class)]
class PassthroughValidatorTest extends FeatureTestCase
{
	#[\PHPUnit\Framework\Attributes\Test]
	public function passing_and_getting_error(): void
	{
		$val = new PassthroughValidator();

		$this->assertTrue($val->passes());
		$this->assertCount(0, $val->errors());
	}
}
