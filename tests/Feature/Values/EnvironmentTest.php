<?php

namespace CraigPaul\Moneris\Tests\Feature\Values;

use PHPUnit\Framework\Attributes\Test;
use CraigPaul\Moneris\Tests\FeatureTestCase;
use CraigPaul\Moneris\Values\Environment;

class EnvironmentTest extends FeatureTestCase
{
	#[Test]
	public function testing_environment(): void
	{
		$env = Environment::testing();

		$this->assertSame(Environment::TESTING, $env->value());
		$this->assertFalse($env->isLive());
	}

	#[Test]
	public function staging_environment(): void
	{
		$env = Environment::staging();

		$this->assertSame(Environment::STAGING, $env->value());
		$this->assertFalse($env->isLive());
	}

	#[Test]
	public function live_environment(): void
	{
		$env = Environment::live();

		$this->assertSame(Environment::LIVE, $env->value());
		$this->assertTrue($env->isLive());
	}
}
