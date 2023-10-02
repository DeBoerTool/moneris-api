<?php

namespace CraigPaul\Moneris\Support\Http;

interface HttpConfigInterface
{
    public function getFullUrl(): string;

    public function getHeaders(): array;

    public function getTimeout(): int;
}
