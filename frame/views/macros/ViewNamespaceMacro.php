<?php namespace frame\views\macros;

use frame\views\View;
use frame\macros\Macro;

abstract class ViewNamespaceMacro extends Macro
{
    private $namespace;

    public function __construct(string $namespace)
    {
        $this->namespace = $namespace;
    }

    public function exec(...$args)
    {
        /** @var View $view */
        $view = $args[0];
        if ($view->isInNamespace($this->namespace)) {
            $this->run($view);
        } 
    }

    protected abstract function run(View $view);
}