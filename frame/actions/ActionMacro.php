<?php namespace frame\actions;

use frame\Core;
use frame\macros\Macro;
use frame\actions\Action;
use frame\actions\ActionRouter;
use frame\actions\ActionTransmitter;
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
            $transmitter = new ActionTransmitter;
            $transmitter->save($action);
            Response::setUrl(Router::toUrlOf($redirect));
        }
    }

    private function getRedirect(Action $action): ?string
    {
        if (!$action->hasErrors()) return $action->getBody()->getSuccessRedirect();
        else return $action->getBody()->getFailRedirect();
    }
}