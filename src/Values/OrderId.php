<?php

namespace CraigPaul\Moneris\Values;

use CraigPaul\Moneris\Interfaces\DataInterface;
use Stringable;

class OrderId implements DataInterface, Stringable
{
    public function __construct(public readonly string $orderId)
    {
    }

    public static function of(self|string $amount): self
    {
        return is_string($amount)
            ? new self($amount)
            : $amount;
    }

    public function __toString(): string
    {
        return $this->orderId;
    }

    public function toArray(): array
    {
        return ['order_id' => $this->orderId];
    }
}
