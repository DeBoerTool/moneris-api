<?php

namespace CraigPaul\Moneris\Support\Cards;

use CraigPaul\Moneris\Cards\Expiry\Expiry;
use CraigPaul\Moneris\Cards\Expiry\Pan;
use CraigPaul\Moneris\Enums\CardType;
use SimpleXMLElement;

abstract class CardAbstract implements CardInterface
{
    protected readonly Pan $pan;

    protected readonly Expiry $expiry;

    public function __construct(
        protected readonly CardType $type,
        Pan|string $pan,
        Expiry|string $expiry,
    ) {
        $this->pan = Pan::of($pan);
        $this->expiry = Expiry::of($expiry);
    }

    public function getType(): CardType
    {
        return $this->type;
    }

    public function getPan(): Pan
    {
        return $this->pan;
    }

    public function getExpiry(): Expiry
    {
        return $this->expiry;
    }

    public function cloneWithExpiry(Expiry|string $expiry): CardInterface
    {
        return new static(
            pan: $this->pan,
            expiry: $expiry,
        );
    }

    public function toArray(): array
    {
        return [
            'pan' => (string) $this->pan,
            'expdate' => (string) $this->expiry,
        ];
    }

    public function addXml(SimpleXMLElement $element): void
    {
        $element->addChild('pan', (string) $this->pan);
        $element->addChild('expdate', (string) $this->expiry);
    }
}
