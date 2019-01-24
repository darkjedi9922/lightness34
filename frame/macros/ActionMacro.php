<?php namespace frame\macros;

use frame\actions\Action;
use frame\Core;
use frame\tools\Json;

use function lightlib\http_parse_query;

class ActionMacro extends Macro
{
    public function exec($action)
    {
        $args = http_parse_query($action, ';');
        $name = explode('_', $args['action']);
        $id = $name[0];
        $class = $name[1];
        $action = Action::$_current = $class::instance($args, $id);
        $action->setData('post', $_POST);

        $ruleDir = Core::$app->config->{'actions.validationConfigFolder'};
        if ($ruleDir) {
            $class = get_class($action);
            $classPath = str_replace('\\', '/', $class);
            $configFile = $ruleDir . '/' . $classPath . '.json';
            if (file_exists($configFile)) {
                $config = new Json($configFile);
                $action->setValidationConfig($config);
            }
        }

        $action->exec();
    }
}