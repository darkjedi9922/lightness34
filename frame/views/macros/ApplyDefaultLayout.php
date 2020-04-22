<?php namespace frame\views\macros;

use frame\events\Macro;
use frame\views\View;
use frame\views\Layouted;

use frame\stdlib\cash\config;

class ApplyDefaultLayout extends Macro
{
    private $layouts = [];

    public function __construct()
    {
        $this->layouts = config::get('layouts')->getData();
        if (isset($this->layouts['namespaces']))
            krsort($this->layouts['namespaces']);
    }

    public function exec(...$args)
    {
        /** @var View $view */
        $view = $args[0];
        $namespace = $view->getNamespace();
        $name = ($namespace ? $namespace . '/' : '') . $view->name;
        if ($view instanceof Layouted) {
            /** @var Layouted $view */
            if ($view->getLayout() === null) {
                if (array_key_exists($name, $this->layouts['names'] ?? [])) {
                    $view->setLayout($this->layouts['names'][$name]);
                    return;
                }
                foreach ($this->layouts['namespaces'] as $namespace => $layout) {
                    if ($view->isInNamespace($namespace)) {
                        $view->setLayout($layout);
                        return;
                    }
                }
            }
        }
    }
}