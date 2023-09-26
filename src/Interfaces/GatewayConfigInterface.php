<?php

namespace CraigPaul\Moneris\Interfaces;

interface GatewayConfigInterface
{
    public function getConnectionConfig(): ConnectionConfigInterface;

    public function useAvs(): bool;

    public function useCvd(): bool;

    public function useCof(): bool;

    public function forVault(): self;

    public function withFeatures(
        bool $useAvs = false,
        bool $useCvd = false,
        bool $useCof = false,
    ): self;
}
