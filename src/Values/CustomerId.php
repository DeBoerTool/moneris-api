<?php

namespace CraigPaul\Moneris\Values;

use CraigPaul\Moneris\Interfaces\DataInterface;
use InvalidArgumentException;
use Stringable;

class CustomerId implements DataInterface, Stringable
{
    public function __construct(public readonly string $customerId)
    {
        if (strpbrk($this->customerId, '<>$%=?^{}[]\\') !== false) {
            throw new InvalidArgumentException(
                'Customer IDs must not contain any of the following characters: < > $ % = ? ^ { } [ ] \\',
            );
        }
    }

    public static function optional(self|string|null $customerId): self|null
    {
        return is_null($customerId)
            ? null
            : self::of($customerId);
    }

    public static function of(self|string $customerId): self
    {
        return new self((string) $customerId);
    }

    public function __toString(): string
    {
        return $this->customerId;
    }

    public function toArray(): array
    {
        return ['cust_id' => $this->customerId];
    }
}
