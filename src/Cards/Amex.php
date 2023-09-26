<?php

namespace CraigPaul\Moneris\Cards;

use CraigPaul\Moneris\Cards\Expiry\Expiry;
use CraigPaul\Moneris\Enums\CardType;
use CraigPaul\Moneris\Interfaces\CardInterface;

class Amex extends CardAbstract implements CardInterface
{
    public function __construct(string $number, Expiry|string $expiry)
    {
        parent::__construct(CardType::AmericanExpress, $number, $expiry);
    }

    public function supportsCof(): bool
    {
        return false;
    }
}
