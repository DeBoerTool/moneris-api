<?php

namespace CraigPaul\Moneris\Enums;

/**
 * An enumeration representing all the possible encryption schemes supported
 * by Moneris.
 *
 * This package only uses SslEnabledMerchant, but the others are included for
 * completeness.
 */
enum CryptEnum: int
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
