<?php

namespace CraigPaul\Moneris\Values;

use CraigPaul\Moneris\Interfaces\DataInterface;
use Stringable;

class Amount implements DataInterface, Stringable
{
    public function __construct(public readonly string $amount)
    {
    }

    public static function of(float|self|string $amount): static
    {
        if ($amount instanceof Amount) {
            return new static($amount->amount);
        }

        if (is_float($amount)) {
            return self::fromFloat($amount);
        }

        return new static($amount);
    }

    public static function fromFloat(float $amount): static
    {
        return new static(
            number_format(
                num: $amount,
                decimals: 2,
                thousands_separator: '',
            ),
        );
    }

    public function toCaptureAmount(): CaptureAmount
    {
        return new CaptureAmount($this->amount);
    }

    public function __toString(): string
    {
        return $this->amount;
    }

    public function toArray(): array
    {
        return ['amount' => $this->amount];
    }
}
