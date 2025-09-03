<?php

namespace CraigPaul\Moneris\Tests\Support;

class AvsPennyValue
{
	public static array $approved = [
		'A' => '10.10',
		'U' => '10.12',
		'Y' => '10.13',
		'N' => '10.17',
		'Z' => '10.18',
		'R' => '10.19',
	];

	public static array $declined = [
		'Y' => '10.24',
		'U' => '10.26',
	];

	public static function approvedFullMatch(): string
	{
		return self::approved('Y');
	}

	public static function approved(string $code): string
	{
		return self::$approved[$code];
	}

	public static function declined(string $code): string
	{
		return self::$declined['code'];
	}
}
