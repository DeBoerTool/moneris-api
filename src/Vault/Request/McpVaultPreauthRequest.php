<?php

namespace CraigPaul\Moneris\Vault\Request;

use CraigPaul\Moneris\Abstract\TransactionRequestAbstract;
use CraigPaul\Moneris\Enums\Currency;
use CraigPaul\Moneris\Enums\McpVersion;
use CraigPaul\Moneris\Enums\TransactionType;
use CraigPaul\Moneris\Vault\Value\DataKey;

readonly class McpVaultPreauthRequest extends TransactionRequestAbstract
{
	public function __construct(
		public DataKey $dataKey,
		public string $orderId,
		public string $amount,
		public Currency $currency,
		public string $cardholderAmount = '100',
		public McpVersion $mcpVersion = McpVersion::One,
	) {
	}

	public function getTransactionType(): TransactionType
	{
		return TransactionType::McpVaultPreauth;
	}

	public function getData(): array
	{
		return [
			'data_key' => (string) $this->dataKey,
			'order_id' => $this->orderId,
			'amount' => $this->amount,
			'cardholder_currency_code' => $this->currency->value,
			'cardholder_amount' => $this->cardholderAmount,
			'mcp_version' => $this->mcpVersion->value,
		];
	}
}
