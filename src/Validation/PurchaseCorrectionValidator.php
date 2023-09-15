<?php

namespace CraigPaul\Moneris\Validation;

class PurchaseCorrectionValidator extends ValidatorAbstract
{
    protected array $mustBeSet = [
        'order_id',
        'txn_number',
    ];

    protected function validate(): void
    {
        foreach ($this->mustBeSet as $key) {
            $this->mustBeSet($key);
        }
    }
}
