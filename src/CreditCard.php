<?php

namespace CraigPaul\Moneris;

use CraigPaul\Moneris\Traits\GettableTrait;
use CraigPaul\Moneris\Traits\SettableTrait;
use CraigPaul\Moneris\Values\Crypt;

/**
 * @property-read \CraigPaul\Moneris\Values\Crypt $crypt
 * @property \CraigPaul\Moneris\Customer|null $customer
 * @property string $expiry
 * @property string $number
 */
class CreditCard
{
    use GettableTrait, SettableTrait;

    protected Crypt $crypt;

    protected Customer|null $customer = null;

    protected string $expiry;

    protected string $number;

    public function __construct(
        string $number,
        string $expiry,
        Crypt|null $crypt = null
    ) {
        $this->number = $number;
        $this->expiry = $expiry;
        $this->crypt = $crypt ?? Crypt::sslEnableMerchant();
    }

    /**
     * Static constructor.
     */
    public static function create(
        string $number,
        string $expiry,
        Crypt|null $crypt = null,
    ): self {
        return new static($number, $expiry, $crypt);
    }

    /**
     * Set the customer.
     */
    public function attach(Customer $customer): self
    {
        $this->customer = $customer;

        return $this;
    }
}
