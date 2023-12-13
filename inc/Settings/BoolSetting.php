<?php

namespace Ferparmur\WpStaticSiteGenerator\Settings;

use JetBrains\PhpStorm\Pure;

class BoolSetting extends AbstractSetting
{
    #[Pure] public function __construct(string $key)
    {
        parent::__construct($key);
        $this->validator = function (mixed $value): bool {
            return ! (empty($value) || $value === 'no' || $value === 'false');
        };
        $this->defaultValue = false;
    }

    public function getValue(): bool
    {
        return (bool)$this->getValueUntyped();
    }
}
