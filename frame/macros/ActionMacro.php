<?php namespace frame\macros;

use frame\Core;
use frame\actions\Action;
use frame\actions\ActionRouter;
use frame\route\Response;
use frame\route\Router;

class ActionMacro extends Macro
{
    public function exec($action)
    {
        $router = new ActionRouter;
        $action = $router->fromTriggerUrl(Core::$app->router->url);
        $action->exec();

        $redirect = $this->getRedirect($action);
        if ($redirect !== null) {
            Response::setUrl(Router::toUrlOf($redirect));
        }
    }

    private function getRedirect(Action $action): ?string
    {
        if (!$action->hasErrors()) return $action->getSuccessRedirect();
        else return $action->getFailRedirect();
    }
}