<?php

namespace CraigPaul\Moneris\Validation;

class AddCardValidator extends ValidatorAbstract
{
	protected array $mustBeSet = [
		'pan',
		'expdate',
	];

	/**
	 * 2026-01-02 - Despite what the Moneris docs say, the COF Info does not
	 *              appear to be required when adding a card.
	 */
	protected array $mustBeSetWithCof = [
		// 'issuer_id',
	];

	protected function validate(): void
	{
		foreach ($this->mustBeSet as $key) {
			$this->mustBeSet($key);
		}

		if ($this->gateway->cof) {
			foreach ($this->mustBeSetWithCof as $key) {
				$this->mustBeSet($key);
			}
		}
	}
}
