<?php

namespace CraigPaul\Moneris\Config;

use CraigPaul\Moneris\Support\Http\HttpConfigInterface;
use CraigPaul\Moneris\Support\Setup\CredentialsInterface;
use CraigPaul\Moneris\Values\ApiToken;
use CraigPaul\Moneris\Values\StoreId;
use SimpleXMLElement;

class Credentials implements CredentialsInterface
{
    public function __construct(
        public readonly HttpConfigInterface $connectionConfig,
        public readonly string|StoreId $storeId,
        public readonly string|ApiToken $apiToken,
        public readonly bool $useAvs = false,
        public readonly bool $useCvd = false,
        public readonly bool $useCof = false,
    ) {
    }

    public function getApiToken(): ApiToken
    {
        return new ApiToken((string) $this->apiToken);
    }

    public function getStoreId(): StoreId
    {
        return new StoreId((string) $this->storeId);
    }

    public function getConnectionConfig(): HttpConfigInterface
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

    public function forVault(): CredentialsInterface
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
    ): CredentialsInterface {
        return new self(
            connectionConfig: $this->connectionConfig,
            storeId: $this->storeId,
            apiToken: $this->apiToken,
            useAvs: $useAvs,
            useCvd: $useCvd,
            useCof: $useCof,
        );
    }

    public function addXml(SimpleXMLElement $element): void
    {
        // The order of these elements matters. If the API token comes first
        // the request will fail.
        $this->getStoreId()->addXml($element);
        $this->getApiToken()->addXml($element);
    }
}
