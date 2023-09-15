<?php

namespace CraigPaul\Moneris\Validation\Errors;

class NotSetError implements ErrorInterface
{
    public function __construct(private string $field)
    {
    }

    public function code(): int
    {
        return 2;
    }

    public function message(): string
    {
        return sprintf('Required field "%s" not set.', $this->field);
    }

    public function field(): string
    {
        return $this->field;
    }

    public function jsonSerialize(): array
    {
        return [
            'code' => $this->code(),
            'message' => $this->message(),
            'field' => $this->field(),
        ];
    }
}
