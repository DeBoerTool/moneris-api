<?php

namespace CraigPaul\Moneris\Vault\Value;

use Stringable;

readonly class DataKey implements Stringable
{
	public function __construct(public string $dataKey) {}

	public function __toString(): string
	{
		return $this->dataKey;
	}
}
