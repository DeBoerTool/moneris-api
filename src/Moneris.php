<?php

/** @noinspection PhpPureAttributeCanBeAddedInspection */

namespace CraigPaul\Moneris;

use CraigPaul\Moneris\Interfaces\GatewayInterface;
use CraigPaul\Moneris\Interfaces\MonerisInterface;
use CraigPaul\Moneris\Traits\GettableTrait;
use CraigPaul\Moneris\Values\Environment;

/**
 * CraigPaul\Moneris\Moneris
 *
 * @property-read string $id
 * @property-read string $token
 * @property-read Environment $environment
 * @property-read bool $avs
 * @property-read bool $cvd
 * @property-read bool $cof
 */
class Moneris implements MonerisInterface
{
    use GettableTrait;

    public function __construct(
        protected string $id,
        protected string $token,
        protected Environment $environment,
        protected bool $avs = false,
        protected bool $cvd = false,
        protected bool $cof = false,
    ) {
    }

    /**
     * Get a new Gateway instance.
     */
    public static function create(
        string $id,
        string $token,
        Environment $environment,
        bool $avs = false,
        bool $cvd = false,
        bool $cof = false,
    ): GatewayInterface {
        return self::gateway($id, $token, $environment, $avs, $cvd, $cof);
    }

    /**
     * Get a new Gateway instance.
     */
    public static function gateway(
        string $id,
        string $token,
        Environment $environment,
        bool $avs = false,
        bool $cvd = false,
        bool $cof = false,
    ): GatewayInterface {
        return (new static($id, $token, $environment, $avs, $cvd, $cof))
            ->connect();
    }

    /**
     * Get a new Vault instance.
     */
    public static function vault(
        string $id,
        string $token,
        Environment $environment,
        bool $avs = false,
        bool $cvd = false,
        bool $cof = false,
    ): GatewayInterface {
        return self::gateway($id, $token, $environment, $avs, $cvd, $cof)
            ->vault();
    }

    /**
     * Get a new Gateway instance.
     */
    public function connect(): GatewayInterface
    {
        return new Gateway(
            $this->id,
            $this->token,
            $this->environment,
            $this->avs,
            $this->cvd,
            $this->cof,
        );
    }
}
