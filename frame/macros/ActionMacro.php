<?php namespace frame\macros;

use frame\Core;
use frame\actions\Action;
use frame\actions\UploadedFile;
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
        $action->setDataAll(Action::DATA_GET, Core::$app->router->args);
        $action->setDataAll(Action::DATA_POST, $_POST);
        $action->setDataAll(Action::DATA_FILES, array_map(function($field) {
            return new UploadedFile($field);
        }, array_keys($_FILES)));

        $ruleDir = Core::$app->config->{'actions.validationConfigFolder'};
        if ($ruleDir) {
            $class = get_class($action);
            $classPath = str_replace('\\', '/', $class);
            $configFile = $ruleDir . '/' . $classPath . '.json';
            if (file_exists($configFile)) {
                $config = new Json($configFile);
                $action->setConfig($config);
            }
        }

        $action->exec();
    }
}