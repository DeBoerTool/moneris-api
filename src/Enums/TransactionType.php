<?php

namespace CraigPaul\Moneris\Enums;

enum TransactionType: string
{
	case McpVaultPreauth = 'mcp_res_preauth_cc';
	case McpVaultPurchase = 'mcp_res_purchase_cc';
}
