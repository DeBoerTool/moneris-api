<?php

namespace CraigPaul\Moneris\Vault\Cof;

enum PaymentInformation: string
{
	case InitialTransaction = '0';
	case SubsequentTransaction = '2';
}
