<?php

namespace CraigPaul\Moneris\TestSupport\Verification;

/**
 * @see https://developer.moneris.com/More/Testing/E-Fraud%20Simulator
 */
class VisaSource extends CardSourceAbstract
{
    protected function cards(): array
    {
        return [
            ['4761739012345603', 'Approved', 'D', 'M'],
            ['4761739012345611', 'Approved', 'G', 'M'],
            ['4761739012345629', 'Approved', 'I', 'M'],
            ['4761739012345637', 'Approved', 'M', 'M'],
            ['4761739012345645', 'Approved', 'N', 'M'],
            ['4761739012345652', 'Approved', 'P', 'M'],
            ['4761739012345660', 'Approved', 'R', 'M'],
            ['4761739012345678', 'Approved', 'A', 'M'],
            ['4761739012345686', 'Approved', 'B', 'M'],
            ['4761739012345694', 'Approved', 'C', 'M'],
            ['4761739012345702', 'Approved', 'U', 'M'],
            ['4761739012347104', 'Approved', 'Y', 'M'],
            ['4761739012345728', 'Approved', 'Z', 'M'],
            ['4761739012347005', 'Declined from Issuer', 'Y', 'M'],
            ['4761739012345751', 'Declined Call for Auth', 'S', 'M'],
            ['4761739012345744', 'Declined from Issuer', 'U', 'M'],
            ['4761739012347112', 'Approved', 'Y', 'P'],
            ['4761739012347120', 'Approved', 'Y', 'M'],
            ['4761739012347138', 'Approved', 'Y', 'N'],
            ['4761739012345793', 'Approved', 'Y', 'S'],
            ['4761739012347146', 'Approved', 'Y', 'U'],
            ['4761739012347013', 'Declined Call for Auth invalid CVV2', 'Y', 'N'],
            ['4761739012347021', 'Declined from Issuer', 'Y', 'M'],
            ['4761739012347039', 'Declined Call For Auth', 'Z', 'M'],
            ['4761739012345850', 'Approved', 'X', 'M'],
            ['4761739012347153', 'Approved', 'Z', 'S'],
            ['4012001038443335', 'Approved', 'N', 'N'],
            ['4012001038488884', 'Approved', 'C', 'M'],
            ['4012001037414112', 'Approved', 'B', 'M'],
            ['4005559876540',    'Approved', 'A', 'S'],
            ['4012001037167778', 'Approved', 'F', 'U'],
            ['4012001037461114', 'Approved', 'P', 'P'],
        ];
    }
}
