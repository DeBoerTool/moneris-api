<?php

namespace CraigPaul\Moneris\Interfaces;

use CraigPaul\Moneris\Cards\Expiry\Expiry;
use CraigPaul\Moneris\Cards\Expiry\Pan;
use CraigPaul\Moneris\Enums\CardType;

interface CardInterface extends DataInterface
{
    public function supportsCof(): bool;

    public function getPan(): Pan;

    public function getExpiry(): Expiry;

    public function getType(): CardType;

    public function cloneWithExpiry(Expiry|string $expiry): self;
}
