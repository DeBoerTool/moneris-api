<?php

namespace CraigPaul\Moneris\Support\Values;

use Stringable;

class StringValue implements Stringable
{
    public function __construct(public readonly string $value)
    {
        if ($this instanceof ValidateInterface) {
            $this->validate();
        }
    }

    public static function of(string|Stringable|StringValue $value): static
    {
        return new static((string) $value);
    }

    public static function optional(
        string|Stringable|StringValue|null $value,
    ): self|null {
        return is_null($value)
            ? null
            : self::of($value);
    }

    public function equals(string|Stringable|StringValue $value): bool
    {
        return $this->value === (string) $value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
