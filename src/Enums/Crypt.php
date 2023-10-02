<?php

namespace CraigPaul\Moneris\Enums;

use CraigPaul\Moneris\Support\Xml\AddXmlInterface;
use SimpleXMLElement;

/**
 * An enumeration representing all the possible encryption schemes supported
 * by Moneris.
 *
 * This package only uses SslEnabledMerchant, but the others are included for
 * completeness.
 */
enum Crypt: int implements AddXmlInterface
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

    public function addXml(SimpleXMLElement $element): void
    {
        $element->addChild('crypt_type', $this->value);
    }
}
