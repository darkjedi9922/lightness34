<?php namespace frame\macros;

use frame\macros\GetMacro;
use frame\views\Widget;

class WidgetMacro extends GetMacro
{
    protected function triggerExec(string $value)
    {
        ob_clean();
        echo new Widget($value);
        exit;
    }
}