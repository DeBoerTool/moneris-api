<?php

namespace CraigPaul\Moneris\Validation;

class UpdateCardValidator extends ValidatorAbstract
{
	/**
	 * 2025-12-31 - Updates only require the data_key.
	 */
	protected array $mustBeSet = [
		'data_key',
	];

	protected array $mustBeSetWithCof = [
		'issuer_id',
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
