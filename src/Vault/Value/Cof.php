<?php

namespace CraigPaul\Moneris\Vault\Value;

use CraigPaul\Moneris\Vault\Cof\PaymentIndicator;
use CraigPaul\Moneris\Vault\Cof\PaymentInformation;

readonly class Cof
{
	public function __construct(
		public PaymentInformation $paymentInformation = PaymentInformation::SubsequentTransaction,
		public PaymentIndicator $paymentIndicator = PaymentIndicator::SubsequentUnscheduledMerchant,
		public string|null $issuerId = null,
	) {}

	public static function initial(): self
	{
		return new self(
			PaymentInformation::InitialTransaction,
			PaymentIndicator::InitialUnscheduled,
		);
	}

	public static function subsequent(
		string $issuerId,
		bool $isCustomer = true,
	): self
	{
		$indicator = $isCustomer
			? PaymentIndicator::SubsequentUnscheduledCustomer
			: PaymentIndicator::SubsequentUnscheduledMerchant;

		return new self(
			PaymentInformation::SubsequentTransaction,
			$indicator,
			$issuerId,
		);
	}

	public function toArray(): array
	{
		return [
			'payment_information' => $this->paymentInformation->value,
			'payment_indicator' => $this->paymentIndicator->value,
			'issuer_id' => $this->issuerId,
		];
	}
}
