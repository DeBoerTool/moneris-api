<?php

namespace CraigPaul\Moneris\Validation;

class AddCardValidator extends ValidatorAbstract
{
    protected array $mustBeSet = [
        'pan',
        'expdate',
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
