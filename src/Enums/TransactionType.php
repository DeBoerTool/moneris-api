<?php

namespace CraigPaul\Moneris\Enums;

enum TransactionType: string
{
    case Purchase = 'purchase';
    case Preauth = 'preauth';
    case Capture = 'completion';
    case Refund = 'refund';
    case Correction = 'purchasecorrection';

    case Verification = 'card_verification';

    case VaultPreauth = 'res_preauth_cc';

    case VaultPurchase = 'res_purchase_cc';

    case VaultAddCard = 'res_add_cc';

    case VaultUpdateCard = 'res_update_cc';

    case VaultDeleteCard = 'res_delete';

    case VaultPeek = 'res_lookup_masked';

    case VaultTokenize = 'res_tokenize_cc';

    case VaultVerification = 'res_card_verification_cc';
}
