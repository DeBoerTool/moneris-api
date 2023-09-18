<?php

namespace CraigPaul\Moneris\Validation\Errors;

use ArrayIterator;
use Countable;
use IteratorAggregate;
use JsonSerializable;
use Stringable;
use Traversable;

class ErrorList implements Countable, IteratorAggregate, JsonSerializable, Stringable
{
    /** @var \CraigPaul\Moneris\Validation\Errors\ErrorInterface[] */
    private array $errors;

    public function __construct(ErrorInterface ...$errors)
    {
        $this->errors = [];

        $this->push(...$errors);
    }

    public static function of(ErrorInterface ...$errors): self
    {
        return new self(...$errors);
    }

    public function push(ErrorInterface ...$errors): self
    {
        foreach ($errors as $error) {
            $this->errors[] = $error;
        }

        return $this;
    }

    public function merge(self $errors): self
    {
        return ErrorList::of(...$this->errors)->push(...$errors->all());
    }

    public function has(): bool
    {
        return $this->count() > 0;
    }

    public function get(int $index): ErrorInterface
    {
        return $this->errors[$index];
    }

    public function all(): array
    {
        return $this->errors;
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->errors);
    }

    public function count(): int
    {
        return count($this->errors);
    }

    public function jsonSerialize(): array
    {
        return $this->errors;
    }

    /**
     * @throws \JsonException
     */
    public function __toString(): string
    {
        return json_encode($this->jsonSerialize(), JSON_THROW_ON_ERROR);
    }
}
