<?php

namespace CraigPaul\Moneris\Validation;

class RefundValidator extends ValidatorAbstract
{
    protected array $mustBeSet = [
        'amount',
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
