<?php

namespace Ferparmur\WpStaticSiteGenerator\Utilities;

class HtmlPage
{
    private string $html;

    public function __construct(string $html)
    {
        $this->html = $html;
    }

    public function findAndReplace()
    {
        $this->html = str_replace('https://wp.htg.local', 'https://www.htg.local', $this->html);
    }

    public function getHtml(): string
    {
        return $this->html;
    }
}
