<?php

namespace CraigPaul\Moneris\Values;

class CaptureAmount extends Amount
{
    public function toArray(): array
    {
        return ['comp_amount' => $this->amount];
    }
}
