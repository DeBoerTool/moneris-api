<?php

namespace CraigPaul\Moneris\Data;

use CraigPaul\Moneris\Vault\Cof\PaymentIndicator;
use CraigPaul\Moneris\Vault\Cof\PaymentInformation;

class CofInfo
{
	public static function initial(): array
	{
		return [
			'payment_indicator' => PaymentIndicator::InitialUnscheduled->value,
			'payment_information' => PaymentInformation::InitialTransaction->value,
			'issuer_id' => null,
		];
	}

	public static function subsequent(bool $isCustomer = true): array
	{
		return [
			'payment_indicator' => $isCustomer
				? PaymentIndicator::SubsequentUnscheduledCustomer->value
				: PaymentIndicator::SubsequentUnscheduledMerchant->value,
			'payment_information' => PaymentInformation::SubsequentTransaction->value,
			'issuer_id' => null,
		];
	}
}
