<?php

namespace CraigPaul\Moneris\Validation;

use CraigPaul\Moneris\Interfaces\GatewayInterface;
use CraigPaul\Moneris\Validation\Errors\ErrorList;
use CraigPaul\Moneris\Validation\Errors\NotSetError;

abstract class ValidatorAbstract implements ValidatorInterface
{
    protected bool|null $passes = null;

    protected ErrorList $errors;

    public function __construct(
        protected GatewayInterface $gateway,
        protected array $params
    ) {
        $this->errors = new ErrorList();
    }

    public static function of(GatewayInterface $gateway, array $params): static
    {
        return new static($gateway, $params);
    }

    abstract protected function validate(): void;

    public function passes(): bool
    {
        if (is_null($this->passes)) {
            $this->validate();

            $this->passes = count($this->errors) === 0;
        }

        return $this->passes;
    }

    public function errors(): ErrorList
    {
        return $this->errors;
    }

    protected function mustBeSet(string $key): void
    {
        if (!isset($this->params[$key])) {
            $this->errors()->push(new NotSetError($key));
        }
    }
}
