<?php

namespace CraigPaul\Moneris\Validation;

class DeleteCardValidator extends ValidatorAbstract
{
    protected array $mustBeSet = [
        'data_key',
    ];

    protected function validate(): void
    {
        foreach ($this->mustBeSet as $key) {
            $this->mustBeSet($key);
        }
    }
}
