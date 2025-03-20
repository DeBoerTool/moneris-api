<?php

namespace CraigPaul\Moneris;

use CraigPaul\Moneris\Traits\GettableTrait;
use CraigPaul\Moneris\Traits\SettableTrait;
use CraigPaul\Moneris\Values\Crypt;

/**
 * @property-read Crypt $crypt
 * @property Customer|null $customer
 * @property string $expiry
 * @property string $number
 */
class CreditCard
{
	use GettableTrait;
	use SettableTrait;

	protected Crypt $crypt;

	protected Customer|null $customer = null;

	public function __construct(
		protected string $number,
		protected string $expiry,
		Crypt|null $crypt = null
	) {
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
