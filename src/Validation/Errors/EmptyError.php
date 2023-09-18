<?php

namespace CraigPaul\Moneris\Validation\Errors;

class EmptyError implements ErrorInterface
{
    public function code(): int
    {
        return 1;
    }

    public function message(): string
    {
        return 'No parameters were provided.';
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
