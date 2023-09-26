<?php

namespace CraigPaul\Moneris\Interfaces;

interface ConnectionConfigInterface
{
    public function getFullUrl(): string;

    public function getHeaders(): array;

    public function getTimeout(): int;
}
