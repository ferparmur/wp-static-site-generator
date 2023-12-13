<?php

namespace Ferparmur\WpStaticSiteGenerator;

class Config
{
    private static Config $instance;
    private array $settingDefinitions;
    private array $settings;

    private function __construct()
    {
        $this->settingDefinitions = include 'Admin/settings.php';
        $this->settings = $this->loadSettings();
    }

    public static function getInstance(): static
    {
        if ( ! isset(self::$instance)) {
            self::$instance = new static();
        }

        return self::$instance;
    }

    public function getSetting(string $settingKey): mixed
    {
        return $this->settings[$settingKey];
    }

    public function getSettings(): array
    {
        return $this->settings;
    }

    private function loadSettings(): array
    {
        $settings = [];
        foreach ($this->settingDefinitions as $settingKey => $settingDefinition) {
            $value = WPSSG_OPTIONS[$settingKey];

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
