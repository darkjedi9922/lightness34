<?php namespace tests\views\examples;

use frame\views\macros\ViewNamespaceMacro;
use frame\views\View;

class ViewNamespaceMacroExample extends ViewNamespaceMacro
{
    public $isRun = false;

    protected function run(View $view)
    {
        $this->isRun = true;
    }
}