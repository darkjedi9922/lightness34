<?php namespace frame\macros;

use frame\Core;
use frame\actions\Action;
use frame\actions\ActionRouter;

class ActionMacro extends Macro
{
    public function exec($action)
    {
        $router = new ActionRouter;
        $action = $router->fromTriggerUrl(Core::$app->router->url);
        $action->exec();
    }
}