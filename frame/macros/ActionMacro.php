<?php namespace frame\macros;

use frame\Core;
use frame\actions\Action;
use frame\actions\UploadedFile;
use frame\config\Json;

use function lightlib\http_parse_query;

class ActionMacro extends Macro
{
    const VALIDATION_CONFIG_FOLDER = 'public/actions';

    public function exec($action)
    {
        $args = http_parse_query($action, ';');
        $name = explode('_', $args['action']);
        $id = $name[0];
        $class = $name[1];
        
        $action = Action::$_current = $class::instance($args, $id);
        $action->setDataAll(Action::DATA_GET, Core::$app->router->args);
        $action->setDataAll(Action::DATA_POST, $_POST);
        $action->setDataAll(Action::DATA_FILES, array_map(function($filedata) {
            return new UploadedFile($filedata);
        }, $_FILES));

        $class = get_class($action);
        $classPath = str_replace('\\', '/', $class);
        $configFile = self::VALIDATION_CONFIG_FOLDER . '/' . $classPath . '.json';
        if (file_exists($configFile)) {
            $config = new Json($configFile);
            $action->setConfig($config->getData());
        }

        $action->exec();
    }
}