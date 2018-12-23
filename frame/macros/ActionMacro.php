<?php namespace frame\macros;

use frame\Action;
use frame\Core;

use function lightlib\http_parse_query;

class ActionMacro extends Macro
{
    public function exec($action)
    {
        $args = http_parse_query($action, ';');
        $name = explode('_', $args['action']);
        $id = $name[0];
        $class = $name[1];
        (Action::$_current = $class::instance($args, $id))->exec();
    }
}