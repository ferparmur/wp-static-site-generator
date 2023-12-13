<?php

namespace Ferparmur\WpStaticSiteGenerator;

use Ferparmur\WpStaticSiteGenerator\Settings\AbstractSetting;
use Ferparmur\WpStaticSiteGenerator\Settings\BoolSetting;
use Ferparmur\WpStaticSiteGenerator\Settings\OptionsSetting;
use Ferparmur\WpStaticSiteGenerator\Settings\TextSetting;

class Config
{
    private static Config $instance;
    private array $settings;

    private function __construct()
    {
        $this->loadSettings();
    }

    public static function getInstance(): static
    {
        if ( ! isset(self::$instance)) {
            self::$instance = new static();
        }

        return self::$instance;
    }

    private function loadSettings(): void
    {
        $staticSiteUrl = new TextSetting('static_site_url');
        $this->settings[$staticSiteUrl->getKey()] = $staticSiteUrl;

        $deploymentMethod = new OptionsSetting('deployment_method');
        $deploymentMethod->setOptions([
            'local',
            'test',
        ]);
        $this->settings[$deploymentMethod->getKey()] = $deploymentMethod;

        $localDeploymentDir = new TextSetting('local_deployment_dir');
        $this->settings[$localDeploymentDir->getKey()] = $localDeploymentDir;

        $disableSslVerify = new BoolSetting('disable_ssl_verify');
        $this->settings[$disableSslVerify->getKey()] = $disableSslVerify;
    }

    public function getSetting(string $settingKey): AbstractSetting
    {
        return $this->settings[$settingKey];
    }

    public function getSettingValue(string $settingKey): mixed
    {
        return $this->getSetting($settingKey)->getValue();
    }

    public function getSettings(): array
    {
        return $this->settings;
    }

}
