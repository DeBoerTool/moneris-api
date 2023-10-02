<?php

namespace CraigPaul\Moneris\Support\Setup;

use CraigPaul\Moneris\Support\Http\HttpConfigInterface;
use CraigPaul\Moneris\Support\Xml\AddXmlInterface;
use CraigPaul\Moneris\Values\ApiToken;
use CraigPaul\Moneris\Values\StoreId;

interface CredentialsInterface extends AddXmlInterface
{
    public function getConnectionConfig(): HttpConfigInterface;

    public function getStoreId(): StoreId;

    public function getApiToken(): ApiToken;

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
