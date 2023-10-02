<?php

namespace CraigPaul\Moneris;

use CraigPaul\Moneris\Transactables\AddCard;
use CraigPaul\Moneris\Transactables\DeleteCard;
use CraigPaul\Moneris\Transactables\LookupCard;
use CraigPaul\Moneris\Data\Transactable\Tokenize;
use CraigPaul\Moneris\Transactables\UpdateCard;
use CraigPaul\Moneris\Data\Transactable\UpdateCardDetails;
use CraigPaul\Moneris\Data\Transactable\VaultVerificationData;
use CraigPaul\Moneris\Support\Setup\CredentialsInterface;
use CraigPaul\Moneris\Traits\GettableTrait;
use GuzzleHttp\Client;

class Vault
{
    use GettableTrait;

    protected CredentialsInterface $config;

    /**
     * Since COF is now mandatory for Vault transactions, it is enabled
     * automatically.
     */
    public function __construct(CredentialsInterface $config)
    {
        $this->config = $config->forVault();
    }

    public function getConfig(): CredentialsInterface
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

    public function verify(VaultVerificationData $data): OldResponse
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
    public function add(AddCard $data): OldResponse
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
    public function update(UpdateCard|UpdateCardDetails $data): OldResponse
    {
        return $this->getProcessor()->process(
            $data->getTransaction($this->config),
        );
    }

    /**
     * Delete a credit card from the Vault.
     */
    public function delete(DeleteCard $data): OldResponse
    {
        return $this->getProcessor()->process(
            $data->getTransaction($this->config),
        );
    }

    /**
     * Get all expiring credit cards from the Moneris Vault.
     */
    public function expiring(): OldResponse
    {
        return $this->getProcessor()->process(
            new Transaction($this->config, ['type' => 'res_get_expiring']),
        );
    }

    /**
     * Peek into the Moneris Vault and retrieve a credit card profile
     * associated with a given data key.
     */
    public function peek(LookupCard $data): OldResponse
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
    public function tokenize(Tokenize $data): OldResponse
    {
        return $this->getProcessor()->process(
            $data->getTransaction($this->config),
        );
    }
}
