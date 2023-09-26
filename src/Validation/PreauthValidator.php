<?php

namespace CraigPaul\Moneris\Validation;

class PreauthValidator extends ValidatorAbstract
{
    protected array $mustBeSet = [
        'data_key',
        'order_id',
        'amount',
    ];

    protected array $mustBeSetWithAvs = [
        'avs_street_number',
        'avs_street_name',
        'avs_zipcode',
    ];

    protected array $mustBeSetWithCvd = [
        'cvd_info',
    ];

    protected array $mustBeSetWithCof = [
        'payment_indicator',
        'payment_information',
    ];

    protected function validate(): void
    {
        foreach ($this->mustBeSet as $key) {
            $this->mustBeSet($key);
        }

        if ($this->config->useAvs) {
            foreach ($this->mustBeSetWithAvs as $key) {
                $this->mustBeSet($key);
            }
        }

        if ($this->config->useCvd) {
            foreach ($this->mustBeSetWithCvd as $key) {
                $this->mustBeSet($key);
            }
        }

        if ($this->config->useCof) {
            foreach ($this->mustBeSetWithCof as $key) {
                $this->mustBeSet($key);
            }
        }
    }
}
