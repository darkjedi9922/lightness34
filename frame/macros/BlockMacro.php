<?php namespace frame\macros;

use frame\views\Block;

class BlockMacro extends Macro
{
    public function exec($value)
    {
        ob_clean();
        echo new Block($value);
        exit;
    }
}