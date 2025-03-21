<?php

namespace CraigPaul\Moneris\Abstract;

use CraigPaul\Moneris\Enums\CryptType;

readonly abstract class TransactionRequestAbstract implements TransactionRequestInterface
{
	abstract public function getData(): array;

	public function getCryptType(): CryptType
	{
		return CryptType::SslEnabledMerchant;
	}

	public function toArray(): array
	{
		return [
			'type' => $this->getTransactionType()->value,
			'crypt_type' => $this->getCryptType()->value,
			...$this->getData(),
		];
	}
}
