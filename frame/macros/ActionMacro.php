<?php namespace frame\macros;

use frame\actions\Action;

class ActionMacro extends Macro
{
    public function exec($action)
    {
        $execAction = Action::fromTriggerUrl($action);
        $execAction->exec();
    }
}