<?php namespace frame\actions;

use frame\route\Router;
use frame\actions\Action;
use frame\actions\UploadedFile;

class ActionRouter
{
    public function fromTriggerUrl(string $url): Action
    {
        $router = new Router($url);
        $type = $router->pagename;
        $class = '\\' . str_replace('/', '\\', $type);

        $action = new Action(new $class, $router->args);
        $action->setDataAll(Action::POST, $_POST);
        $action->setDataAll(Action::FILES, array_map(function ($filedata) {
            return new UploadedFile($filedata);
        }, $_FILES));

        return $action;
    }

    public function getTriggerUrl(Action $action)
    {
        return Router::toUrlOf(
            '/' . str_replace('\\', '/', get_class($action->getBody())),
            array_merge(
                [Action::ID => ''],
                $action->getDataArray(true)[Action::ARGS]
            )
        );
    }
}