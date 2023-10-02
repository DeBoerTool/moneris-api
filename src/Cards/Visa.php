<?php

namespace CraigPaul\Moneris\Cards;

use CraigPaul\Moneris\Cards\Expiry\Expiry;
use CraigPaul\Moneris\Enums\CardType;
use CraigPaul\Moneris\Support\Cards\CardAbstract;
use CraigPaul\Moneris\Support\Cards\CardInterface;

class Visa extends CardAbstract implements CardInterface
{
    public function __construct(string $number, Expiry|string $expiry)
    {
        parent::__construct(CardType::Visa, $number, $expiry);
    }

    public function supportsCof(): bool
    {
        return true;
    }
}
