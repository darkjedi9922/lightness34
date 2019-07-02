<?php namespace frame\macros;

use frame\actions\Action;
use frame\Core;

class ActionMacro extends Macro
{
    public function exec($action)
    {
        $action = Action::fromTriggerUrl(Core::$app->router->url);
        $action->exec();
    }
}