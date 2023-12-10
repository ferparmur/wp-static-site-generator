<?php

namespace Ferparmur\WpStaticSiteGenerator\Utilities;

class FakeScreen
{
    public function in_admin($admin = null): bool
    {
        return false;
    }
}
