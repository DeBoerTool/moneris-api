<?php

namespace CraigPaul\Moneris\Vault\Value;

use CraigPaul\Moneris\Vault\Cof\PaymentIndicator;
use CraigPaul\Moneris\Vault\Cof\PaymentInformation;

readonly class Cof
{
	public function __construct(
		public PaymentInformation $paymentInformation = PaymentInformation::SubsequentTransaction,
		public PaymentIndicator $paymentIndicator = PaymentIndicator::U,
		public string|null $issuerId = null,
	) {}

	public function toArray(): array
	{
		return [
			'payment_information' => $this->paymentInformation->value,
			'payment_indicator' => $this->paymentIndicator->value,
			...($this->issuerId
				? ['issuer_id' => $this->issuerId]
				: []),
		];
	}
}
