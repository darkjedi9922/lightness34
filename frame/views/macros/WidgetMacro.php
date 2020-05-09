<?php namespace frame\views\macros;

use frame\route\macros\GetMacro;
use frame\views\Widget;
use frame\route\Response;

class WidgetMacro extends GetMacro
{
    protected function triggerExec(string $value)
    {
        ob_clean();
        (new Widget($value))->show();
        Response::getDriver()->finish();
    }
}