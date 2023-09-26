<?php

namespace CraigPaul\Moneris\Enums;

use InvalidArgumentException;

enum CardType: string
{
    case Visa = 'visa';
    case MasterCard = 'mastercard';
    case AmericanExpress = 'amex';
    case Discover = 'discover';

    public static function fromResponseCode(string $code): self
    {
        return match ($code) {
            'V' => self::Visa,
            'M' => self::MasterCard,
            'AX' => self::AmericanExpress,
            'NO' => self::Discover,
            default => throw new InvalidArgumentException(
                sprintf('Invalid card type "%s".', $code),
            ),
        };
    }
}
