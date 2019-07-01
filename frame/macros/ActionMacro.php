<?php namespace frame\macros;

use frame\actions\Action;

class ActionMacro extends Macro
{
    public function exec($action)
    {
        $action = Action::fromTriggerUrl($action);
        $action->exec();
    }
}