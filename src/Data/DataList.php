<?php

namespace CraigPaul\Moneris\Data;

use Countable;
use CraigPaul\Moneris\Support\Xml\AddXmlInterface;
use SimpleXMLElement;

class DataList implements Countable, AddXmlInterface
{
    /** @var AddXmlInterface[] */
    private array $items;

    public function __construct(AddXmlInterface|array|null ...$items)
    {
        $this->push(...$items);
    }

    public function push(AddXmlInterface|array|null ...$items): self
    {
        foreach ($items as $item) {
            if (is_null($item)) {
                continue;
            }

            if (is_array($item)) {
                if (count($item) === 0) {
                    continue;
                }

                $this->items[] = new ArbitraryData($item);

                continue;
            }

            $this->items[] = $item;
        }

        return $this;
    }

    public function count(): int
    {
        return count($this->items);
    }

    public function addXml(SimpleXMLElement $element): void
    {
        foreach ($this->items as $item) {
            $item->addXml($element);
        }
    }
}
