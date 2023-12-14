<?php

namespace Ferparmur\WpStaticSiteGenerator\Settings;

use JetBrains\PhpStorm\Pure;

class TextSetting extends AbstractSetting
{

    #[Pure] public function __construct(string $key)
    {
        parent::__construct($key);

        $this->validator = function (?string $value): string {
            return is_string($value) && ! empty($value) ? $value : $this->defaultValue;
        };
        $this->defaultValue = '';
    }

    public function getValue(): string
    {
        return (string)$this->getValueUntyped();
    }
}
