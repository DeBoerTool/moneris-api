<?php

namespace CraigPaul\Moneris\Values;

class Environment
{
    const LIVE = 'live';

    const STAGING = 'staging';

    const TESTING = 'testing';

    protected function __construct(private string $environment)
    {
    }

    public static function live(): self
    {
        return new self(self::LIVE);
    }

    public static function staging(): self
    {
        return new self(self::STAGING);
    }

    public static function testing(): self
    {
        return new self(self::TESTING);
    }

    public function value(): string
    {
        return $this->environment;
    }

    public function isLive(): bool
    {
        return $this->value() === self::LIVE;
    }
}
