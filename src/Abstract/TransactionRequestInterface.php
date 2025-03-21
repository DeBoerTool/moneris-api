<?php

namespace CraigPaul\Moneris\Abstract;

use CraigPaul\Moneris\Enums\CryptType;
use CraigPaul\Moneris\Enums\TransactionType;

interface TransactionRequestInterface
{
	/** @return array<string, mixed> */
	public function toArray(): array;

	public function getTransactionType(): TransactionType;

	public function getCryptType(): CryptType;
}
