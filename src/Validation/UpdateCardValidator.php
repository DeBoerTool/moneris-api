<?php

namespace CraigPaul\Moneris\Validation;

class UpdateCardValidator extends ValidatorAbstract
{
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

        if ($this->config->useCof) {
            foreach ($this->mustBeSetWithCof as $key) {
                $this->mustBeSet($key);
            }
        }
    }
}
