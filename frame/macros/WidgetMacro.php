<?php namespace frame\macros;

use frame\views\Widget;

class WidgetMacro extends Macro
{
    public function exec($value)
    {
        ob_clean();
        echo new Widget($value);
        exit;
    }
}