<?php

namespace Ferparmur\WpStaticSiteGenerator;

class Config
{
    private static Config $instance;
    private array $settingDefinitions;
    private array $settings;

    private function __construct()
    {
        $this->settingDefinitions = include 'settings.php';
        $this->settings = $this->loadSettings();
    }

    public static function getInstance(): static
    {
        if ( ! isset(self::$instance)) {
            self::$instance = new static();
        }

        return self::$instance;
    }

    public function isSettingDefinedByConstant(
        string $settingKey
    ): bool {
        return defined('WPSSG_OPTIONS') && isset(WPSSG_OPTIONS[$settingKey]);
    }

    public function getSetting(string $settingKey): mixed
    {
        return $this->settings[$settingKey];
    }

    private function loadSettings(): array
    {
        foreach ($this->settingDefinitions as $settingKey => $settingDefinition) {
            $value = null;
            if ($this->isSettingDefinedByConstant($settingKey)) {
                $value = WPSSG_OPTIONS[$settingKey];
            } else {
                $value = get_option('_wpssg_' . $settingKey);
            }

            switch ($settingDefinition['type']) {
                case 'bool':
                    $value = ! (empty($value) || $value === 'no' || $value === 'false');
                    break;
                case 'select':
                    $value = in_array($value,
                        $settingDefinition['options']) ? $value : $settingDefinition['options'][0];
                    break;
            }

            $settings[$settingKey] = $value;
        }

        return $settings;
    }

}
