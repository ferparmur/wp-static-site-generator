<?php

namespace Ferparmur\WpStaticSiteGenerator\Settings;

use Closure;

abstract class AbstractSetting
{
    protected string $key;
    protected Closure $validator;
    protected mixed $value;
    protected mixed $defaultValue;

    protected function __construct(string $key)
    {
        $this->key = $key;
    }

    protected function setValidation(callable $validator): void
    {
        $this->validator = Closure::fromCallable($validator);
    }

    protected function getValueUntyped(): mixed
    {
        if ( ! isset($this->value)) {
            $this->value = $this->validator->call($this, WPSSG_OPTIONS[$this->key]);
        }

        return $this->value;
    }

    public function getKey(): string
    {
        return $this->key;
    }

    abstract public function getValue();

    public function setDefaultValue(mixed $defaultValue): void
    {
        $this->defaultValue = $defaultValue;
    }
}
