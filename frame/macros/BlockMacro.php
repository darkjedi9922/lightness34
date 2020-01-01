<?php namespace frame\macros;

use frame\macros\GetMacro;
use frame\views\Block;
use frame\route\Response;

class BlockMacro extends GetMacro
{
    protected function triggerExec(string $value)
    {
        ob_clean();
        (new Block($value))->show();
        Response::finish();
    }
}