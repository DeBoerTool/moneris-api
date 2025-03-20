<?php

namespace CraigPaul\Moneris\Tests\Feature\Values;

use PHPUnit\Framework\Attributes\Test;
use CraigPaul\Moneris\Tests\FeatureTestCase;
use CraigPaul\Moneris\Values\Crypt;
use InvalidArgumentException;

class CryptTest extends FeatureTestCase
{
	#[Test]
	public function instantiating_with_valid_type(): void
	{
		$crypt = new Crypt(1);

		$this->assertSame(1, $crypt->value());
	}

	#[Test]
	public function failing_with_invalid_type(): void
	{
		$this->expectException(InvalidArgumentException::class);

		new Crypt(111);
	}

	#[Test]
	public function using_the_static_constructor(): void
	{
		$crypt = Crypt::sslEnableMerchant();

		$this->assertSame(Crypt::SSL_ENABLED_MERCHANT, $crypt->value());
	}

	#[Test]
	public function casting_to_string(): void
	{
		$crypt = Crypt::sslEnableMerchant();

		$this->assertSame('7', (string) $crypt);
	}
}
