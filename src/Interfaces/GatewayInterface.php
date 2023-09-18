<?php

namespace CraigPaul\Moneris\Interfaces;

use CraigPaul\Moneris\Response;
use CraigPaul\Moneris\Transaction;
use CraigPaul\Moneris\Vault;

interface GatewayInterface
{
    /**
     * Capture a pre-authorized transaction.
     */
    public function capture(
        string|Transaction $transaction,
        string|null $order = null,
        mixed $amount = null
    ): Response;

    /**
     * Create a new Vault instance.
     */
    public function cards(): Vault;

    /**
     * Pre-authorize a purchase.
     */
    public function preauth(array $params = []): Response;

    /**
     * Make a purchase.
     */
    public function purchase(array $params = []): Response;

    /**
     * Refund a transaction.
     */
    public function refund(
        string|Transaction $transaction,
        string|null $order = null,
        mixed $amount = null
    ): Response;

    /**
     * Validate CVD and/or AVS prior to attempting a purchase.
     */
    public function verify(array $params = []): Response;

    /**
     * Void a transaction.
     */
    public function void(
        string|Transaction $transaction,
        string|null $order = null
    ): Response;
}
