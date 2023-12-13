<?php

namespace Ferparmur\WpStaticSiteGenerator\Settings;

use JetBrains\PhpStorm\Pure;

class OptionsSetting extends AbstractSetting
{
    private array $options;

    #[Pure] public function __construct(string $key)
    {
        parent::__construct($key);
        $this->validator = function (?string $value): string {
            return in_array($value, $this->options) ? $value : $this->defaultValue;
        };
        $this->options = [];
        $this->defaultValue = '';
    }

    public function getValue(): string
    {
        return (string)$this->getValueUntyped();
    }

    public function setOptions(array $options): void
    {
        $this->options = $options;
    }
}
