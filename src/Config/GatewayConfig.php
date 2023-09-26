<?php

namespace CraigPaul\Moneris\Config;

use CraigPaul\Moneris\Interfaces\ConnectionConfigInterface;
use CraigPaul\Moneris\Interfaces\GatewayConfigInterface;

class GatewayConfig implements GatewayConfigInterface
{
    public function __construct(
        public readonly ConnectionConfigInterface $connectionConfig,
        public readonly string $storeId,
        public readonly string $apiToken,
        public readonly bool $useAvs = false,
        public readonly bool $useCvd = false,
        public readonly bool $useCof = false,
    ) {
    }

    public function getConnectionConfig(): ConnectionConfigInterface
    {
        return $this->connectionConfig;
    }

    public function useAvs(): bool
    {
        return $this->useAvs;
    }

    public function useCvd(): bool
    {
        return $this->useCvd;
    }

    public function useCof(): bool
    {
        return $this->useCof;
    }

    public function forVault(): GatewayConfigInterface
    {
        return new self(
            connectionConfig: $this->connectionConfig,
            storeId: $this->storeId,
            apiToken: $this->apiToken,
            useAvs: $this->useAvs,
            useCvd: $this->useCvd,
            useCof: true,
        );
    }

    public function withFeatures(
        bool $useAvs = false,
        bool $useCvd = false,
        bool $useCof = false,
    ): GatewayConfigInterface {
        return new self(
            connectionConfig: $this->connectionConfig,
            storeId: $this->storeId,
            apiToken: $this->apiToken,
            useAvs: $useAvs,
            useCvd: $useCvd,
            useCof: $useCof,
        );
    }
}
