<?php

namespace CraigPaul\Moneris\Support\Cards;

use CraigPaul\Moneris\Cards\Expiry\Expiry;
use CraigPaul\Moneris\Cards\Expiry\Pan;
use CraigPaul\Moneris\Enums\CardType;
use CraigPaul\Moneris\Support\DataInterface;
use CraigPaul\Moneris\Support\Xml\AddXmlInterface;

interface CardInterface extends DataInterface, AddXmlInterface
{
    public function supportsCof(): bool;

    public function getPan(): Pan;

    public function getExpiry(): Expiry;

    public function getType(): CardType;

    public function cloneWithExpiry(Expiry|string $expiry): self;
}
