<?php

namespace CraigPaul\Moneris\Validation;

class CompletionValidator extends ValidatorAbstract
{
    protected array $mustBeSet = [
        'comp_amount',
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
