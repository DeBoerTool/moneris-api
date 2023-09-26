<?php

namespace CraigPaul\Moneris\Data\Cof;

use CraigPaul\Moneris\Interfaces\DataInterface;

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
class CofAddOrUpdateCardData implements DataInterface
{
    public function __construct(public readonly string $issuerId)
    {
    }

    public function toArray(): array
    {
        return ['issuer_id' => $this->issuerId];
    }
}
