<?php namespace frame\views\macros;

use frame\events\Macro;
use frame\views\View;
use frame\views\Layouted;

use frame\cash\config;

class ApplyDefaultLayout extends Macro
{
    private $layouts = [];

    public function __construct()
    {
        $this->layouts = config::get('layouts')->getData();
        krsort($this->layouts);
    }

    public function exec(...$args)
    {
        /** @var View $view */
        $view = $args[0];
        if ($view instanceof Layouted) {
            /** @var Layouted $view */
            if ($view->getLayout() === null) {
                foreach ($this->layouts as $namespace => $layout) {
                    if ($view->isInNamespace($namespace)) {
                        $view->setLayout($layout);
                        return;
                    }
                }
            }
        }
    }
}