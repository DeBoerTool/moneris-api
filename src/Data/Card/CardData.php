<?php

namespace CraigPaul\Moneris\Data\Card;

use CraigPaul\Moneris\Data\EmptyData;
use CraigPaul\Moneris\Support\Cards\CardInterface;
use CraigPaul\Moneris\Support\DataInterface;

class CardData implements DataInterface
{
    public readonly DataInterface $customer;

    public function __construct(
        public readonly CardInterface $card,
        CardCustomerData|null $customer = null,
    ) {
        $this->customer = $customer ?? new EmptyData();
    }

    public function toArray(): array
    {
        return [
            ...$this->card->toArray(),
            ...$this->customer->toArray(),
        ];
    }
}
