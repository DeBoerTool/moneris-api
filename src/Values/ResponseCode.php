<?php

namespace CraigPaul\Moneris\Values;

class ResponseCode
{
    public function __construct(public readonly string|null $code)
    {
    }

    public function isValid(): bool
    {
        return $this->code !== null;
    }

    public function isSuccessful(): bool
    {
        if (!$this->isValid()) {
            return false;
        }

        return $this->code >= 0 && $this->code < 50;
    }
}
