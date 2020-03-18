<?php namespace frame\macros;

use frame\macros\GetMacro;
use frame\views\Widget;
use frame\route\Response;

class WidgetMacro extends GetMacro
{
    protected function triggerExec(string $value)
    {
        ob_clean();
        (new Widget($value))->show();
        Response::get()->finish();
    }
}