<?php

namespace CraigPaul\Moneris\Tests\Feature\Values;

use CraigPaul\Moneris\Tests\TestCase;
use CraigPaul\Moneris\Values\Environment;

class EnvironmentTest extends TestCase
{
    /** @test */
    public function testing_environment(): void
    {
        $env = Environment::testing();

        $this->assertSame(Environment::TESTING, $env->value());
        $this->assertFalse($env->isLive());
    }

    /** @test */
    public function staging_environment(): void
    {
        $env = Environment::staging();

        $this->assertSame(Environment::STAGING, $env->value());
        $this->assertFalse($env->isLive());
    }

    /** @test */
    public function live_environment(): void
    {
        $env = Environment::live();

        $this->assertSame(Environment::LIVE, $env->value());
        $this->assertTrue($env->isLive());
    }
}
