<?php

namespace CraigPaul\Moneris\Tests\Feature\Traits;

use CraigPaul\Moneris\Tests\FeatureTestCase;
use CraigPaul\Moneris\Traits\SettableTrait;
use InvalidArgumentException;

class SettableTest extends FeatureTestCase
{
	private object $stub;

	public function setUp(): void
	{
		parent::setUp();

		$this->stub = new class {
			use SettableTrait;

			private mixed $myProp = null;

			public function myProp(): mixed
			{
				return $this->myProp;
			}
		};
	}

	#[\PHPUnit\Framework\Attributes\Test]
	public function setting_a_property(): void
	{
		$this->assertNull($this->stub->myProp());

		$this->stub->myProp = 'some test value';

		$this->assertSame('some test value', $this->stub->myProp());
	}

	#[\PHPUnit\Framework\Attributes\Test]
	public function failing_to_set_a_property(): void
	{
		$this->expectException(InvalidArgumentException::class);

		$this->stub->someNonexistantProperty = [];
	}
}
