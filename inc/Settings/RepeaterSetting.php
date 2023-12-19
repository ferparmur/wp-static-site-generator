<?php

namespace Ferparmur\WpStaticSiteGenerator\Settings;

use JetBrains\PhpStorm\Pure;

class RepeaterSetting extends AbstractSetting
{
    private array $settings;

    #[Pure] public function __construct(string $key)
    {
        parent::__construct($key);
        $this->validator = function (?array $value): array {
            return is_array($value) && ! empty($value) ? $value : $this->defaultValue;
        };
        $this->defaultValue = [];
    }

    public function getValue(): array
    {
        $rawValues = $this->getValueUntyped();
        $values = [];
        foreach ($rawValues as $i => $row) {
            foreach ($this->settings as $key => $setting) {
                /** @var $setting AbstractSetting */
                $setting->setSettingsValuesArray($row);
                $values[$i][$key] = $setting->getValue();
            }
        }

        return $values;
    }

    public function addSetting(AbstractSetting $setting): void
    {
        $this->settings[$setting->getKey()] = $setting;
    }
}
