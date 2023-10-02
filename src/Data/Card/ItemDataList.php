<?php

namespace CraigPaul\Moneris\Data\Card;

use Countable;
use CraigPaul\Moneris\Support\Xml\AddXmlInterface;
use CraigPaul\Moneris\Support\DataInterface;
use SimpleXMLElement;

class ItemDataList implements DataInterface, AddXmlInterface, Countable
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

    public function addXml(SimpleXMLElement|null $element): void
    {
        if (count($this) < 1) {
            return;
        }

        foreach ($this->items as $item) {
            $item->addXml($element);
        }
    }

    public function count(): int
    {
        return count($this->items);
    }
}
