<?php

namespace CraigPaul\Moneris\Cards\Expiry;

use CraigPaul\Moneris\Support\Xml\AddXmlInterface;
use CraigPaul\Moneris\Support\DataInterface;
use SimpleXMLElement;
use Stringable;

/**
 * DTO representing the Primary Account Number (PAN) of a credit card.
 */
class Pan implements DataInterface, Stringable, AddXmlInterface
{
    public function __construct(public readonly string $pan)
    {
    }

    public static function of(Pan|string $pan): self
    {
        return is_string($pan)
            ? new self($pan)
            : $pan;
    }

    public function toArray(): array
    {
        return ['pan' => $this->pan];
    }

    public function __toString(): string
    {
        return $this->pan;
    }

    public function addXml(SimpleXMLElement $element): void
    {
        $element->addChild('pan', (string) $this);
    }
}
