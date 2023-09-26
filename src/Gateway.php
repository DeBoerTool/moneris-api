<?php

namespace CraigPaul\Moneris;

use CraigPaul\Moneris\Config\GatewayConfig;
use CraigPaul\Moneris\Data\Transactable\CaptureData;
use CraigPaul\Moneris\Data\Transactable\CorrectionData;
use CraigPaul\Moneris\Data\Transactable\PreauthData;
use CraigPaul\Moneris\Data\Transactable\PurchaseData;
use CraigPaul\Moneris\Data\Transactable\RefundData;
use CraigPaul\Moneris\Data\Transactable\VaultPreauthData;
use CraigPaul\Moneris\Data\Transactable\VaultPurchaseData;
use CraigPaul\Moneris\Data\Transactable\VerificationData;
use CraigPaul\Moneris\Interfaces\GatewayConfigInterface;
use GuzzleHttp\Client;

class Gateway
{
    public function __construct(public readonly GatewayConfig $config)
    {
    }

    public function getConfig(): GatewayConfigInterface
    {
        return $this->config;
    }

    public function getVault(): Vault
    {
        return new Vault($this->config);
    }

    public function getProcessor(): Processor
    {
        return new Processor(
            config: $this->config->connectionConfig,
            guzzle: new Client(),
        );
    }

    public function purchase(PurchaseData|VaultPurchaseData $data): Response
    {
        return $this->process($data->getTransaction($this->config));
    }

    public function preauth(PreauthData|VaultPreauthData $data): Response
    {
        return $this->process($data->getTransaction($this->config));
    }

    /**
     * Capture a pre-authorized transaction.
     */
    public function capture(CaptureData $data): Response
    {
        return $this->getProcessor()->process(
            $data->getTransaction($this->config),
        );
    }

    public function refund(RefundData $data): Response
    {
        return $this->getProcessor()->process(
            $data->getTransaction($this->config),
        );
    }

    public function correction(CorrectionData $data): Response
    {
        return $this->getProcessor()->process(
            $data->getTransaction($this->config),
        );
    }

    public function verify(VerificationData $data): Response
    {
        return $this->getProcessor()->process(
            $data->getTransaction($this->config),
        );
    }

    /**
     * Process a transaction through the Moneris API.
     */
    protected function process(Transaction $transaction): Response
    {
        $processor = new Processor(
            config: $this->config->connectionConfig,
            guzzle: new Client()
        );

        /** @noinspection PhpUnhandledExceptionInspection */
        return $processor->process($transaction);
    }
}
