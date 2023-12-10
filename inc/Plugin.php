<?php

namespace Ferparmur\WpStaticSiteGenerator;

use Ferparmur\WpStaticSiteGenerator\Admin\Menu;

class Plugin
{
    public function init()
    {
        $menu = new Menu();
        $menu->init();

        $postGenerator = new Generators\Post();
        $postGenerator->init();
    }

}
