<?php namespace frame\macros;

use frame\actions\Action;
use frame\Core;

class ActionMacro extends Macro
{
    public function exec($action)
    {
        $noRuleMode = Core::$app->config->{'actions.noRuleMode'};
        $action = Action::fromTriggerUrl($action, $noRuleMode);
        $action->exec();
    }
}