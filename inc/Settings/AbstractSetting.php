<?php

namespace Ferparmur\WpStaticSiteGenerator\Settings;

use Closure;

abstract class AbstractSetting
{
    protected string $key;
    protected array $settingsValuesArray;
    protected Closure $validator;
    protected mixed $value;
    protected mixed $defaultValue;

    protected function __construct(string $key, array $settingsValuesArray = WPSSG_OPTIONS)
    {
        $this->key = $key;
        $this->settingsValuesArray = $settingsValuesArray;
    }

    protected function setValidation(callable $validator): void
    {
        $this->validator = Closure::fromCallable($validator);
    }

    public function setSettingsValuesArray(array $settingsValuesArray): void
    {
        $this->settingsValuesArray = $settingsValuesArray;
    }

    protected function getValueUntyped(): mixed
    {
        if ( ! isset($this->value)) {
            $this->value = $this->validator->call($this, $this->settingsValuesArray[$this->key] ?? $this->defaultValue);
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
