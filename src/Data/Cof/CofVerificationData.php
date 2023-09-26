<?php

namespace CraigPaul\Moneris\Data\Cof;

use CraigPaul\Moneris\Enums\CofPaymentIndicator;
use CraigPaul\Moneris\Enums\CofPaymentInformation;
use CraigPaul\Moneris\Interfaces\DataInterface;

/**
 * This object represents the COF data required to get an IssuerId from the
 * Moneris API.
 *
 * From the Moneris documentation:
 *
 * -- 3.10 Card Verification and Credential on File Transactions --
 *
 * In the absence of a Purchase or Pre-Authorization, a Card Verification
 * transaction is used to get the unique issuer ID value (issuerId) that is
 * used in subsequent Credential on File transactions. Issuer ID is a variable
 * included in the nested Credential on File Info object.
 *
 * For all first-time transactions, including Card Verification transactions,
 * you must also request the cardholder's Card Verification Details (CVD).
 *
 * The Card Verification request, including the Credential on File Info object,
 * must be sent immediately prior to storing cardholder credentials.
 */
class CofVerificationData implements DataInterface
{
    public function toArray(): array
    {
        return [
            'issuer_id' => '',
            'payment_information' => CofPaymentInformation::First->value,
            'payment_indicator' => CofPaymentIndicator::Unscheduled->value,
        ];
    }
}
