<?php namespace engine\statistics;

use frame\macros\Macro;
use frame\views\DynamicPage;
use frame\views\Page;

class CollectPage extends Macro
{
    /** @var Page */
    public $page = null;

    public function exec(...$args)
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