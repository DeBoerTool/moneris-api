<?php

namespace CraigPaul\Moneris;

use CraigPaul\Moneris\Config\Credentials;
use CraigPaul\Moneris\Data\Transactable\Capture;
use CraigPaul\Moneris\Data\Transactable\Correction;
use CraigPaul\Moneris\Data\Transactable\Preauth;
use CraigPaul\Moneris\Data\Transactable\Purchase;
use CraigPaul\Moneris\Data\Transactable\Refund;
use CraigPaul\Moneris\Transactables\VaultPreauth;
use CraigPaul\Moneris\Data\Transactable\VaultPurchase;
use CraigPaul\Moneris\Transactables\VerifyCard;
use CraigPaul\Moneris\Support\Setup\CredentialsInterface;
use GuzzleHttp\Client;

class Gateway
{
    public function __construct(public readonly Credentials $config)
    {
    }

    public function getConfig(): CredentialsInterface
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

    public function purchase(Purchase|VaultPurchase $data): OldResponse
    {
        return $this->process($data->getTransaction($this->config));
    }

    public function preauth(Preauth|VaultPreauth $data): OldResponse
    {
        return $this->process($data->getTransaction($this->config));
    }

    /**
     * Capture a pre-authorized transaction.
     */
    public function capture(Capture $data): OldResponse
    {
        return $this->getProcessor()->process(
            $data->getTransaction($this->config),
        );
    }

    public function refund(Refund $data): OldResponse
    {
        return $this->getProcessor()->process(
            $data->getTransaction($this->config),
        );
    }

    public function correction(Correction $data): OldResponse
    {
        return $this->getProcessor()->process(
            $data->getTransaction($this->config),
        );
    }

    public function verify(VerifyCard $data): OldResponse
    {
        return $this->getProcessor()->process(
            $data->getTransaction($this->config),
        );
    }

    /**
     * Process a transaction through the Moneris API.
     */
    protected function process(Transaction $transaction): OldResponse
    {
        $processor = new Processor(
            config: $this->config->connectionConfig,
            guzzle: new Client()
        );

        /** @noinspection PhpUnhandledExceptionInspection */
        return $processor->process($transaction);
    }
}
