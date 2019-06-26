<?php namespace frame\macros;

use frame\actions\Action;
use frame\Core;

class ActionMacro extends Macro
{
    public function exec($action)
    {
        $noRuleMode = Core::$app->config->{'actions.noRuleMode'};
        Action::$_current = Action::fromTriggerUrl($action, $noRuleMode);
        Action::$_current->exec();
    }
}