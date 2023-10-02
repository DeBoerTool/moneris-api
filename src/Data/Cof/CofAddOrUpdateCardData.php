<?php

namespace CraigPaul\Moneris\Data\Cof;

use CraigPaul\Moneris\Support\Xml\AddXmlInterface;
use CraigPaul\Moneris\Support\DataInterface;
use CraigPaul\Moneris\Values\IssuerId;
use SimpleXMLElement;

/**
 * This object represents the COF data required to get an IssuerId from the
 * Moneris API.
 *
 * From the Moneris documentation:
 *
 * -- For Vault Add Credit Card transactions --
 *
 * 1. Send Card Verification transaction request including the Credential on
 * File object to get the Issuer ID
 * 2. Send the Vault Add Credit Card request including the Credential on File
 * Info object (Issuer ID only)
 */
class CofAddOrUpdateCardData implements DataInterface, AddXmlInterface
{
    public readonly IssuerId $issuerId;

    public function __construct(string|IssuerId|null $issuerId)
    {
        // Certain issuers don't support CoF, in which case the issuer id
        // returned will be null. We don't want to fail in that case, or have
        // every transactable check for null, so we'll just set it to an empty
        // string.
        $this->issuerId = IssuerId::of($issuerId ?? '');
    }

    public function toArray(): array
    {
        return ['cof_info' => [...$this->issuerId->toArray()]];
    }

    public function addXml(SimpleXMLElement $element): void
    {
        $cof = $element->addChild('cof_info');

        $this->issuerId->addXml($cof);
    }
}
