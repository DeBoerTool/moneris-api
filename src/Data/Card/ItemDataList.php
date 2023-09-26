<?php

namespace CraigPaul\Moneris\Data\Card;

use CraigPaul\Moneris\Interfaces\DataInterface;

class ItemDataList implements DataInterface
{
    /** @var \CraigPaul\Moneris\Data\Card\ItemData[] */
    private array $items;

    public function __construct(ItemData ...$items)
    {
        $this->items = $items;
    }

    public function toArray(): array
    {
        return array_map(fn (ItemData $item) => $item->toArray(), $this->items);
    }
}
