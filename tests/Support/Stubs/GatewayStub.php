<?php

namespace CraigPaul\Moneris\Tests\Support\Stubs;

use CraigPaul\Moneris\Interfaces\GatewayInterface;
use CraigPaul\Moneris\Response;
use CraigPaul\Moneris\Transaction;
use CraigPaul\Moneris\Vault;

class GatewayStub implements GatewayInterface
{
    public function capture(string|Transaction $transaction, string|null $order = null, mixed $amount = null): Response
    {
        exit('Stub. Not Implemented.');
    }

    public function cards(): Vault
    {
        exit('Stub. Not Implemented.');
    }

    public function preauth(array $params = []): Response
    {
        exit('Stub. Not Implemented.');
    }

    public function purchase(array $params = []): Response
    {
        exit('Stub. Not Implemented.');
    }

    public function refund(string|Transaction $transaction, string|null $order = null, mixed $amount = null): Response
    {
        exit('Stub. Not Implemented.');
    }

    public function verify(array $params = []): Response
    {
        exit('Stub. Not Implemented.');
    }

    public function void(string|Transaction $transaction, string|null $order = null): Response
    {
        exit('Stub. Not Implemented.');
    }

    public function getValidAvsCodes(): array
    {
        exit('Stub. Not Implemented.');
    }

    public function hasAvsEnabled(): bool
    {
        exit('Stub. Not Implemented.');
    }

    public function isValidAvsCode(string $code): bool
    {
        exit('Stub. Not Implemented.');
    }

    public function getValidCvdCodes(): array
    {
        exit('Stub. Not Implemented.');
    }

    public function hasCvdEnabled(): bool
    {
        exit('Stub. Not Implemented.');
    }

    public function isValidCvdCode(string $code): bool
    {
        exit('Stub. Not Implemented.');
    }
}
