<?php

namespace CraigPaul\Moneris\Data\Card;

use CraigPaul\Moneris\Interfaces\DataInterface;

// 'item' => ['name', 'quantity', 'product_code', 'extended_amount'],
class ItemData implements DataInterface
{
    public function __construct(
        public readonly string $name = '',
        public readonly string $quantity = '',
        public readonly string $productCode = '',
        public readonly string $extendedAmount = '',
    ) {
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'quantity' => $this->quantity,
            'product_code' => $this->productCode,
            'extended_amount' => $this->extendedAmount,
        ];
    }
}
