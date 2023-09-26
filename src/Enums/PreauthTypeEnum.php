<?php

namespace CraigPaul\Moneris\Enums;

enum PreauthTypeEnum: string
{
    case Preauth = 'preauth';
    case VaultPreauth = 'res_preauth_cc';
}
