<?php namespace frame\macros;

use frame\macros\GetMacro;
use frame\views\Value;

class ValueMacro extends GetMacro
{
    protected function triggerExec(string $value)
    {
        ob_clean();
        (new Value($value))->show();
        exit;
    }
}