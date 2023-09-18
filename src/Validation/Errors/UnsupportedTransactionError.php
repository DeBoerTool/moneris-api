<?php

namespace CraigPaul\Moneris\Validation\Errors;

class UnsupportedTransactionError implements ErrorInterface
{
    public function code(): int
    {
        return 3;
    }

    public function message(): string
    {
        return 'Unsupported transaction type.';
    }

    public function jsonSerialize(): array
    {
        return [
            'code' => $this->code(),
            'message' => $this->message(),
            'field' => null,
        ];
    }
}
