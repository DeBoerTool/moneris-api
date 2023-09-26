<?php

namespace CraigPaul\Moneris\Values;

use CraigPaul\Moneris\Interfaces\DataInterface;
use Stringable;

class DataKey implements DataInterface, Stringable
{
    public function __construct(public readonly string $dataKey)
    {
    }

    public static function of(self|string $dataKey): self
    {
        return new self((string) $dataKey);
    }

    public function __toString(): string
    {
        return $this->dataKey;
    }

    public function toArray(): array
    {
        return ['data_key' => $this->dataKey];
    }
}
