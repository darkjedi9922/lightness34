<?php namespace frame\macros;

use frame\views\Value;

class ValueMacro extends Macro
{
    public function exec($value)
    {
        ob_clean();
        echo new Value($value);
        exit;
    }
}