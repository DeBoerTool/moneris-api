<?php

namespace CraigPaul\Moneris\Traits;

use InvalidArgumentException;

trait GettableTrait
{
    /**
     * Retrieve a property off of the class.
     *
     * @throws \InvalidArgumentException
     */
    public function __get(string $property): mixed
    {
        if (property_exists($this, $property)) {
            return $this->$property;
        }

        throw new InvalidArgumentException(sprintf(
            '[%s] does not contain a property named [%s]',
            self::class,
            $property
        ));
    }
}
