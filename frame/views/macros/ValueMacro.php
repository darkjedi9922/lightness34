<?php namespace frame\views\macros;

use frame\events\GetMacro;
use frame\views\Value;
use frame\route\Response;

class ValueMacro extends GetMacro
{
    protected function triggerExec(string $value)
    {
        ob_clean();
        (new Value($value))->show();
        Response::getDriver()->finish();
    }
}