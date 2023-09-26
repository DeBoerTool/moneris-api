<?php

namespace CraigPaul\Moneris\Values;

use CraigPaul\Moneris\Interfaces\DataInterface;
use CraigPaul\Moneris\Transaction;
use Stringable;

class TransactionNumber implements DataInterface, Stringable
{
    public function __construct(public readonly string $transactionNumber)
    {
    }

    public static function of(self|string|Transaction $transactionNumber): self
    {
        return $transactionNumber instanceof Transaction
            ? new self($transactionNumber->number())
            : new self((string) $transactionNumber);
    }

    public function __toString(): string
    {
        return $this->transactionNumber;
    }

    public function toArray(): array
    {
        return ['txn_number' => $this->transactionNumber];
    }
}
