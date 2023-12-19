<?php use Ferparmur\WpStaticSiteGenerator\Config; ?>

<h1>Generate Static Site</h1>

<div>
    <button class="button button-primary">Generate Site</button>


    <?php var_dump(Config::getInstance()->getSettingValue('crawl_starting_paths')); ?>
</div>
