<?php namespace engine\statistics\macros;

use frame\views\DynamicPage;
use frame\views\Page;

class CollectPageRouteStat extends BaseStatCollector
{
    /** @var Page */
    public $page = null;

    protected function collect(...$args)
    {
        $view = $args[0];
        switch (get_class($view)) {
            case Page::class:
            case DynamicPage::class:
                $this->page = $view;
                break;
        }
    }
}