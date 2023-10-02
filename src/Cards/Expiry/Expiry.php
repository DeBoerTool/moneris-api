<?php

namespace CraigPaul\Moneris\Cards\Expiry;

use CraigPaul\Moneris\Support\Xml\AddXmlInterface;
use CraigPaul\Moneris\Support\DataInterface;
use SimpleXMLElement;
use Stringable;

class Expiry implements DataInterface, Stringable, AddXmlInterface
{
    public function __construct(public readonly string $yymm)
    {
    }

    public static function of(self|string $expiry): self
    {
        return is_string($expiry)
            ? new self($expiry)
            : $expiry;
    }

    public static function fromMonthAndYear(string $month, string $year): static
    {
        return new static(sprintf('%s%s', $month, $year));
    }

    public function toArray(): array
    {
        return ['expdate' => $this->yymm];
    }

    public function __toString(): string
    {
        return $this->yymm;
    }

    public function addXml(SimpleXMLElement $element): void
    {
        $element->addChild('expdate', (string) $this);
    }
}
