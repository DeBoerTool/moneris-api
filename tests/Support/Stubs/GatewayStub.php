<?php

namespace CraigPaul\Moneris\Tests\Support\Stubs;

use CraigPaul\Moneris\Interfaces\GatewayInterface;
use CraigPaul\Moneris\Response;
use CraigPaul\Moneris\Transaction;
use CraigPaul\Moneris\Vault;

class GatewayStub implements GatewayInterface
{

    public function capture(string|Transaction $transaction, ?string $order = null, mixed $amount = null): Response
    {
        die('Stub. Not Implemented.');
    }

    public function cards(): Vault
    {
        die('Stub. Not Implemented.');
    }

    public function preauth(array $params = []): Response
    {
        die('Stub. Not Implemented.');
    }

    public function purchase(array $params = []): Response
    {
        die('Stub. Not Implemented.');
    }

    public function refund(string|Transaction $transaction, ?string $order = null, mixed $amount = null): Response
    {
        die('Stub. Not Implemented.');
    }

    public function verify(array $params = []): Response
    {
        die('Stub. Not Implemented.');
    }

    public function void(string|Transaction $transaction, ?string $order = null): Response
    {
        die('Stub. Not Implemented.');
    }

    public function getValidAvsCodes(): array
    {
        die('Stub. Not Implemented.');
    }

    public function hasAvsEnabled(): bool
    {
        die('Stub. Not Implemented.');
    }

    public function isValidAvsCode(string $code): bool
    {
        die('Stub. Not Implemented.');
    }

    public function getValidCvdCodes(): array
    {
        die('Stub. Not Implemented.');
    }

    public function hasCvdEnabled(): bool
    {
        die('Stub. Not Implemented.');
    }

    public function isValidCvdCode(string $code): bool
    {
        die('Stub. Not Implemented.');
    }
}
