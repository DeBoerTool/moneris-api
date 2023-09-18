<?php

namespace CraigPaul\Moneris\Traits;

use InvalidArgumentException;

trait SettableTrait
{
    /**
     * Set a property that exists on the class.
     *
     * @throws \InvalidArgumentException
     */
    public function __set(string $property, mixed $value): void
    {
        if (property_exists($this, $property)) {
            $this->$property = $value;
        } else {
            throw new InvalidArgumentException(sprintf(
                '[%s] does not contain a property named [%s]',
                $this::class,
                $property
            ));
        }
    }
}
