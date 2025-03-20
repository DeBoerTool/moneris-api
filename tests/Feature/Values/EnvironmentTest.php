<?php

namespace CraigPaul\Moneris\Tests\Feature\Values;

use CraigPaul\Moneris\Tests\FeatureTestCase;
use CraigPaul\Moneris\Values\Environment;

class EnvironmentTest extends FeatureTestCase
{
	#[\PHPUnit\Framework\Attributes\Test]
	public function testing_environment(): void
	{
		$env = Environment::testing();

		$this->assertSame(Environment::TESTING, $env->value());
		$this->assertFalse($env->isLive());
	}

	#[\PHPUnit\Framework\Attributes\Test]
	public function staging_environment(): void
	{
		$env = Environment::staging();

		$this->assertSame(Environment::STAGING, $env->value());
		$this->assertFalse($env->isLive());
	}

	#[\PHPUnit\Framework\Attributes\Test]
	public function live_environment(): void
	{
		$env = Environment::live();

		$this->assertSame(Environment::LIVE, $env->value());
		$this->assertTrue($env->isLive());
	}
}
