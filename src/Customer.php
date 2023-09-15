<?php

namespace CraigPaul\Moneris;

use CraigPaul\Moneris\Traits\PreparableTrait;
use InvalidArgumentException;

/**
 * @property array $data
 * @property string $email
 * @property string $id
 * @property string $note
 * @property string $phone
 */
class Customer
{
    use PreparableTrait;

    /**
     * The Customer data.
     */
    protected array $data = [];

    public function __construct(array $params = [])
    {
        $this->data = $this->prepare($params, [
            ['property' => 'id', 'key' => 'id'],
            ['property' => 'email', 'key' => 'email'],
            ['property' => 'phone', 'key' => 'phone'],
            ['property' => 'note', 'key' => 'note'],
        ]);
    }

    /**
     * Static constructor
     */
    public static function create(array $params = []): static
    {
        return new static($params);
    }

    /**
     * Retrieve a property off of the class or from the data array.
     *
     * @throws \InvalidArgumentException
     */
    public function __get(string $property): mixed
    {
        if (property_exists($this, $property)) {
            return $this->$property;
        }

        if (isset($this->data[$property]) && !is_null($this->data[$property])) {
            return $this->data[$property];
        }

        throw new InvalidArgumentException(sprintf(
            '[%s] does not contain a property named [%s]',
            $this::class,
            $property
        ));
    }

    /**
     * Set a property that exists on the class, otherwise add it to the data
     * array.
     */
    public function __set(string $property, mixed $value): void
    {
        if (property_exists($this, $property)) {
            $this->$property = $value;

            return;
        }

        $this->data[$property] = $value;
    }
}
