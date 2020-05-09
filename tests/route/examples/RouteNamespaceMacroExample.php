<?php namespace tests\route\examples;

use frame\route\RouteNamespaceMacro;

class RouteNamespaceMacroExample extends RouteNamespaceMacro
{
    public $wasRun = false;

    protected function run()
    {
        $this->wasRun = true;
    }
}