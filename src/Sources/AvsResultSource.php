<?php

namespace CraigPaul\Moneris\Sources;

use CraigPaul\Moneris\Enums\AvsCode;
use CraigPaul\Moneris\Enums\CardType;
use CraigPaul\Moneris\Values\AvsResult;
use InvalidArgumentException;

class AvsResultSource
{
    public static function resolve(
        CardType $cardType,
        AvsCode $avsCode,
    ): AvsResult {
        foreach ((new self())->data() as $result) {
            if ($result['code'] === $avsCode->value) {
                return new AvsResult(
                    cardType: $cardType,
                    avsCode: $avsCode,
                    message: $result[$cardType->value],
                );
            }
        }

        throw new InvalidArgumentException('No AVS result found.');
    }

    public static function data(): array
    {
        return [
            [
                'code' => 'A',
                'visa' => 'AVS street address only partial match',
                'mastercard' => 'Address matches, postal code does not.',
                'discover' => 'Address matches, five-digit postal code matches',
                'amex' => 'Billing address matches, zip code does not.',
            ],
            [
                'code' => 'D',
                'visa' => 'n/a',
                'mastercard' => 'n/a',
                'discover' => 'n/a',
                'amex' => 'Customer name incorrect, postal code matches',
            ],
            [
                'code' => 'E',
                'visa' => 'n/a',
                'mastercard' => 'n/a',
                'discover' => 'n/a',
                'amex' => 'Customer name incorrect, billing address and postal code match',
            ],
            [
                'code' => 'F',
                'visa' => 'n/a',
                'mastercard' => 'n/a',
                'discover' => 'n/a',
                'amex' => 'Customer name incorrect, billing address matches.',
            ],
            [
                'code' => 'G',
                'visa' => 'n/a',
                'mastercard' => 'n/a',
                'discover' => 'Address information not verified for international transaction',
                'amex' => 'n/a',
            ],
            [
                'code' => 'K',
                'visa' => 'n/a',
                'mastercard' => 'n/a',
                'discover' => 'n/a',
                'amex' => 'Customer name matches',
            ],
            [
                'code' => 'L',
                'visa' => 'n/a',
                'mastercard' => 'n/a',
                'discover' => 'n/a',
                'amex' => 'Customer name and postal code match.',
            ],
            [
                'code' => 'M',
                'visa' => 'n/a',
                'mastercard' => 'n/a',
                'discover' => 'n/a',
                'amex' => 'Customer name, billing address, and postal code match.',
            ],
            [
                'code' => 'N',
                'visa' => 'AVS non-match',
                'mastercard' => 'Neither address nor postal code match.',
                'discover' => 'Neither address nor postal code match.',
                'amex' => 'Billing address and postal code do not match.',
            ],
            [
                'code' => 'O',
                'visa' => 'n/a',
                'mastercard' => 'n/a',
                'discover' => 'n/a',
                'amex' => 'Customer name and billing address match',
            ],
            [
                'code' => 'R',
                'visa' => '(AVS indeterminate outcome (retry)) V.I.P. will convert invalid values in Field 44.2 to R (AVS indeterminate outcome (retry)).',
                'mastercard' => 'Retry; system unable to process.',
                'discover' => 'n/a',
                'amex' => 'System unavailable; retry.',
            ],
            [
                'code' => 'S',
                'visa' => 'n/a',
                'mastercard' => 'AVS currently not supported.',
                'discover' => 'AVS currently not supported.',
                'amex' => 'AVS currently not supported.',
            ],
            [
                'code' => 'T',
                'visa' => 'n/a',
                'mastercard' => 'n/a',
                'discover' => 'Nine-digit zip code matches, address does not match.',
                'amex' => 'n/a',
            ],
            [
                'code' => 'U',
                'visa' => '(AVS unable to verify) V.I.P. will convert a blank space in Field 44.2 to U (AVS unable to verify).',
                'mastercard' => 'No data from Issuer/Authorization system.',
                'discover' => 'Retry; system unable to process.',
                'amex' => 'Information is unavailable.',
            ],
            [
                'code' => 'W',
                'visa' => 'n/a',
                'mastercard' => 'For U.S. Addresses, nine-digit postal code matches, address does not; for address outside the U.S. postal code matches, address does not.',
                'discover' => 'No data from Issuer/Authorization system',
                'amex' => 'Customer name, billing address, and postal code are all correct.',
            ],
            [
                'code' => 'X',
                'visa' => 'n/a',
                'mastercard' => 'For U.S. addresses, nine-digit postal code and addresses matches; for addresses outside the U.S., postal code and address match.',
                'discover' => 'Address matches, nine-digit postal code matches',
                'amex' => 'n/a',
            ],
            [
                'code' => 'Y',
                'visa' => '(AVS full match)',
                'mastercard' => 'For U.S. addresses, five-digit postal code and address matches.',
                'discover' => 'Address matches, postal code does not',
                'amex' => 'Billing address and postal code both match',
            ],
            [
                'code' => 'Z',
                'visa' => '(AVS postal/zip code only (partial match))',
                'mastercard' => 'For U.S. addresses, five-digit postal code matches, address does not match.',
                'discover' => 'Five-digit postal code matches, address does not',
                'amex' => 'Postal code matches, billing address does not.',
            ],
        ];
    }
}
