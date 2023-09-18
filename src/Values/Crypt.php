<?php

namespace CraigPaul\Moneris\Values;

use InvalidArgumentException;
use ReflectionClass;
use Stringable;

final class Crypt implements Stringable
{
    public const MAIL_TELEPHONE_SINGLE = 1;

    public const MAIL_TELEPHONE_RECURRING = 2;

    public const MAIL_TELEPHONE_INSTALLMENT = 3;

    public const MAIL_TELEPHONE_UNKNOWN = 4;

    public const AUTHENTICATED_E_COMMERCE = 5;

    public const NON_AUTHENTICATED_E_COMMERCE = 6;

    public const SSL_ENABLED_MERCHANT = 7;

    public const NON_SECURE = 8;

    public const NON_AUTHENTICATED = 9;

    private int $crypt;

    public function __construct(int $crypt)
    {
        $validValues = array_values(
            (new ReflectionClass($this))->getConstants()
        );

        $this->crypt = in_array($crypt, $validValues)
            ? $crypt
            : throw new InvalidArgumentException('Invalid Crypt type.');
    }

    public static function sslEnableMerchant(): self
    {
        return new self(self::SSL_ENABLED_MERCHANT);
    }

    public function value(): int
    {
        return $this->crypt;
    }

    public function __toString(): string
    {
        return (string) $this->value();
    }
}
