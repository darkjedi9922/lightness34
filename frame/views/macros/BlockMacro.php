<?php namespace frame\views\macros;

use frame\events\GetMacro;
use frame\route\Response;
use frame\views\Block;

class BlockMacro extends GetMacro
{
    protected function triggerExec(string $value)
    {
        ob_clean();
        (new Block($value))->show();
        Response::getDriver()->finish();
    }
}