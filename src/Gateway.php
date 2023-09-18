<?php

namespace CraigPaul\Moneris;

use CraigPaul\Moneris\Interfaces\GatewayInterface;
use CraigPaul\Moneris\Traits\GettableTrait;
use CraigPaul\Moneris\Traits\SettableTrait;
use CraigPaul\Moneris\Values\Crypt;
use CraigPaul\Moneris\Values\Environment;
use GuzzleHttp\Client;

/**
 * CraigPaul\Moneris\Gateway
 *
 * @property bool $avs
 * @property-read array $avsCodes
 * @property bool $cvd
 * @property-read array $cvdCodes
 * @property-read string $environment
 * @property-read string $id
 * @property-read string $token
 * @property \CraigPaul\Moneris\Transaction $transaction
 * @property bool $cof
 */
class Gateway implements GatewayInterface
{
    use GettableTrait, SettableTrait;

    protected array $avsCodes = ['A', 'B', 'D', 'M', 'P', 'W', 'X', 'Y', 'Z'];

    protected array $cvdCodes = ['M', 'Y', 'P', 'S', 'U'];

    public function __construct(
        protected string $id,
        protected string $token,
        protected Environment $environment,
        protected bool $avs = false,
        protected bool $cvd = false,
        protected bool $cof = false,
        protected Transaction|null $transaction = null,
    ) {
    }

    /**
     * Capture a pre-authorized transaction.
     *
     * @param string|null $order
     */
    public function capture(
        string|Transaction $transaction,
        string|null $order = null,
        mixed $amount = null
    ): Response {
        $transactionNumber = $transaction;

        if ($transaction instanceof Transaction) {
            $order = $transaction->order();

            $amount = is_null($amount)
                ? $transaction->amount()
                : $amount;

            $transactionNumber = $transaction->number();
        }

        $params = [
            'type' => 'completion',
            'crypt_type' => Crypt::SSL_ENABLED_MERCHANT,
            'comp_amount' => $amount,
            'txn_number' => $transactionNumber,
            'order_id' => $order,
        ];

        $transaction = $this->transaction($params);

        return $this->process($transaction);
    }

    /**
     * Alias for self::vault().
     */
    public function cards(): Vault
    {
        return $this->vault();
    }

    /**
     * Create a new Vault instance.
     */
    public function vault(): Vault
    {
        return new Vault(
            $this->id,
            $this->token,
            $this->environment,
            $this->avs,
            $this->cvd,
            $this->cof,
        );
    }

    /**
     * Pre-authorize a purchase.
     */
    public function preauth(array $params = []): Response
    {
        $params = array_merge($params, [
            'type' => 'preauth',
            'crypt_type' => Crypt::SSL_ENABLED_MERCHANT,
        ]);

        $transaction = $this->transaction($params);

        return $this->process($transaction);
    }

    /**
     * Make a purchase.
     */
    public function purchase(array $params = []): Response
    {
        $params = array_merge($params, [
            'type' => 'purchase',
            'crypt_type' => Crypt::SSL_ENABLED_MERCHANT,
        ]);

        $transaction = $this->transaction($params);

        return $this->process($transaction);
    }

    /**
     * Refund a transaction.
     */
    public function refund(
        string|Transaction $transaction,
        string|null $order = null,
        mixed $amount = null
    ): Response {
        if ($transaction instanceof Transaction) {
            $order = $transaction->order();
            $amount = is_null($amount)
                ? $transaction->amount()
                : $amount;
            $transaction = $transaction->number();
        }

        $params = [
            'type' => 'refund',
            'crypt_type' => Crypt::SSL_ENABLED_MERCHANT,
            'amount' => $amount,
            'txn_number' => $transaction,
            'order_id' => $order,
        ];

        $transaction = $this->transaction($params);

        return $this->process($transaction);
    }

    /**
     * Validate CVD and/or AVS prior to attempting a purchase.
     */
    public function verify(array $params = []): Response
    {
        $params = array_merge($params, [
            'type' => 'card_verification',
            'crypt_type' => Crypt::SSL_ENABLED_MERCHANT,
        ]);

        $transaction = $this->transaction($params);

        return $this->process($transaction);
    }

    /**
     * Void a transaction.
     */
    public function void(
        string|Transaction $transaction,
        string|null $order = null
    ): Response {
        if ($transaction instanceof Transaction) {
            $order = $transaction->order();
            $transaction = $transaction->number();
        }

        $params = [
            'type' => 'purchasecorrection',
            'crypt_type' => Crypt::SSL_ENABLED_MERCHANT,
            'txn_number' => $transaction,
            'order_id' => $order,
        ];

        $transaction = $this->transaction($params);

        return $this->process($transaction);
    }

    /**
     * Process a transaction through the Moneris API.
     */
    protected function process(Transaction $transaction): Response
    {
        $processor = new Processor(new Client());

        return $processor->process($transaction);
    }

    /**
     * Get or create a new Transaction instance.
     */
    protected function transaction(array|null $params = null): Transaction
    {
        return !$this->transaction || is_array($params)
            ? $this->transaction = new Transaction($this, $params)
            : $this->transaction;
    }
}
