<?php

namespace CraigPaul\Moneris\Enums;

enum CryptType: int
{
	case MailTelephoneSingle = 1;
	case MailTelephoneRecurring = 2;
	case MailTelephoneInstallment = 3;
	case MailTelephoneUnknown = 4;
	case AuthenticatedECommerce = 5;
	case NonAuthenticatedECommerce = 6;
	case SslEnabledMerchant = 7;
	case NonSecure = 8;
	case NonAuthenticated = 9;
}
