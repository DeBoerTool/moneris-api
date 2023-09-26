<?php

namespace CraigPaul\Moneris;

use CraigPaul\Moneris\Data\Transactable\AddCardData;
use CraigPaul\Moneris\Data\Transactable\DeleteCardData;
use CraigPaul\Moneris\Data\Transactable\PeekData;
use CraigPaul\Moneris\Data\Transactable\TokenizeData;
use CraigPaul\Moneris\Data\Transactable\UpdateCardData;
use CraigPaul\Moneris\Data\Transactable\UpdateDetailsData;
use CraigPaul\Moneris\Data\Transactable\VaultVerificationData;
use CraigPaul\Moneris\Interfaces\GatewayConfigInterface;
use CraigPaul\Moneris\Traits\GettableTrait;
use GuzzleHttp\Client;

class Vault
{
    use GettableTrait;

    protected GatewayConfigInterface $config;

    /**
     * Since COF is now mandatory for Vault transactions, it is enabled
     * automatically.
     */
    public function __construct(GatewayConfigInterface $config)
    {
        $this->config = $config->forVault();
    }

    public function getConfig(): GatewayConfigInterface
    {
        return $this->config;
    }

    public function getProcessor(): Processor
    {
        return new Processor(
            config: $this->config->connectionConfig,
            guzzle: new Client(),
        );
    }

    public function verify(VaultVerificationData $data): Response
    {
        return $this->getProcessor()->process(
            new Transaction($this->config, $data),
        );
    }

    /**
     * Add a credit card to the Vault. This should only be done after card
     * verification using COF for cards that support it, as you will need the
     * Issuer ID to add the card.
     */
    public function add(AddCardData $data): Response
    {
        return $this->getProcessor()->process(
            $data->getTransaction($this->config),
        );
    }

    /**
     * Update an existing credit card in the Vault.
     *
     * If you want to update the card number, use the UpdateCardData
     * object. This will require CoF data, specifically the Issuer ID, for
     * cards that support it, meaning that you will need to verify the card
     * again before updating.
     *
     * If you want to update non-card-number data, use the UpdateDetailsData
     * object, which will allow you to update the expiry date, the AVS data,
     * and/or the CardCustomer data. This type of update does not require
     * CoF data, since the Issuer ID will remain the same for the same card
     * number.
     */
    public function update(UpdateCardData|UpdateDetailsData $data): Response
    {
        return $this->getProcessor()->process(
            $data->getTransaction($this->config),
        );
    }

    /**
     * Delete a credit card from the Vault.
     */
    public function delete(DeleteCardData $data): Response
    {
        return $this->getProcessor()->process(
            $data->getTransaction($this->config),
        );
    }

    /**
     * Get all expiring credit cards from the Moneris Vault.
     */
    public function expiring(): Response
    {
        return $this->getProcessor()->process(
            new Transaction($this->config, ['type' => 'res_get_expiring']),
        );
    }

    /**
     * Peek into the Moneris Vault and retrieve a credit card profile
     * associated with a given data key.
     */
    public function peek(PeekData $data): Response
    {
        return $this->getProcessor()->process(
            new Transaction($this->config, $data),
        );
    }

    /**
     * Creates a new credit card profile using the credit card number, expiry
     * date and e-commerce indicator that were submitted in a previous
     * financial transaction.
     *
     * Previous transactions to be tokenized must have included the Credential
     * on File Info object.
     */
    public function tokenize(TokenizeData $data): Response
    {
        return $this->getProcessor()->process(
            $data->getTransaction($this->config),
        );
    }
}
