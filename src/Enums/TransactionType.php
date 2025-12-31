<?php

namespace CraigPaul\Moneris\Enums;

enum TransactionType: string
{
	case McpVaultPreauth = 'mcp_res_preauth_cc';
	case McpVaultPurchase = 'mcp_res_purchase_cc';
	case VaultCardAdd = 'res_add_cc';
	case VaultCardUpdate = 'res_update_cc';
}
