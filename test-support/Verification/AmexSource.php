<?php

namespace CraigPaul\Moneris\TestSupport\Verification;

/**
 * @see https://developer.moneris.com/More/Testing/E-Fraud%20Simulator
 */
class AmexSource extends CardSourceAbstract
{
    protected function cards(): array
    {
        return [
            ['375987000000062', 'Approved', 'Y', 'Y'],
            ['375987000000021', 'Approved', 'N', 'N'],
            ['375987000000013', 'Approved', 'Z', 'Y'],
            ['374500261001009', 'Approved', 'U', 'U'],
            ['375987000000997', 'Declined', 'U', 'N'],
        ];
    }
}
