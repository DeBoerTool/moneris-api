<?php

namespace CraigPaul\Moneris\Tests\Feature;

use CraigPaul\Moneris\Gateway;
use CraigPaul\Moneris\Moneris;
use CraigPaul\Moneris\Tests\FeatureTestCase;
use CraigPaul\Moneris\Vault;
use InvalidArgumentException;

/**
 * @covers \CraigPaul\Moneris\Moneris
 */
class MonerisTest extends FeatureTestCase
{
    /** @test */
    public function instantiation(): void
    {
        $moneris = $this->moneris();

        $this->assertInstanceOf(Moneris::class, $moneris);
        $this->assertPropertiesAreGettable($moneris);
    }

    /** @test */
    public function instantiation_with_optional_params(): void
    {
        $moneris = $this->moneris(avs: true);

        $this->assertTrue($moneris->avs);
        $this->assertFalse($moneris->cvd);
        $this->assertFalse($moneris->cof);

        $moneris = $this->moneris(cvd: true);

        $this->assertFalse($moneris->avs);
        $this->assertTrue($moneris->cvd);
        $this->assertFalse($moneris->cof);

        $moneris = $this->moneris(cof: true);

        $this->assertFalse($moneris->avs);
        $this->assertFalse($moneris->cvd);
        $this->assertTrue($moneris->cof);
    }

    /** @test */
    public function getting_the_gateway_via_static_method(): void
    {
        $gateway = Moneris::create($this->id, $this->token, $this->environment);

        $this->assertInstanceOf(Gateway::class, $gateway);
        $this->assertPropertiesAreGettable($gateway);
    }

    /** @test */
    public function getting_the_vault_via_static_method(): void
    {
        $vault = Moneris::vault($this->id, $this->token, $this->environment);

        $this->assertInstanceOf(Vault::class, $vault);
        $this->assertPropertiesAreGettable($vault);
    }

    /** @test */
    public function it_fails_to_retrieve_a_non_existent_property_of_the_class()
    {
        $moneris = $this->moneris();

        $this->expectException(InvalidArgumentException::class);

        /** @noinspection PhpExpressionResultUnusedInspection */
        /** @noinspection PhpUndefinedFieldInspection */
        $moneris->nonExistentProperty;
    }

    /** @test */
    public function getting_the_gateway()
    {
        $moneris = $this->moneris();

        $gateway = $moneris->connect();

        $this->assertInstanceOf(Gateway::class, $gateway);
        $this->assertPropertiesAreGettable($gateway);
    }

    protected function assertPropertiesAreGettable(object $object): void
    {
        $this->assertSame($this->id, $object->id);
        $this->assertSame($this->token, $object->token);
        $this->assertSame($this->environment, $object->environment);
        $this->assertFalse($object->avs);
        $this->assertFalse($object->cvd);
        $this->assertFalse($object->cof);
    }
}
