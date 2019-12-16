<?php namespace frame\actions;

use frame\Core;
use frame\actions\Action;
use frame\actions\ActionRouter;
use frame\actions\ActionTransmitter;
use frame\route\Response;
use frame\route\Router;
use frame\route\Request;
use frame\macros\GetMacro;

class ActionMacro extends GetMacro
{
    const EVENT_ACTION_TRIGGERED = 'action-triggered';

    /** @var Action */
    private $action;

    protected function triggerExec(string $value)
    {
        Core::$app->emit(self::EVENT_ACTION_TRIGGERED);
        $router = new ActionRouter;
        $this->action = $router->fromTriggerUrl(Core::$app->router->url);
        $tokenizer = new ActionToken($this->action);
        $tokenizer->validate();
        $this->action->exec();
        $this->result();
    }
    
    private function result()
    {
        if (Request::isAjax()) $this->jsonify();
        else $this->redirect();
    }

    private function jsonify()
    {
        Response::setText(json_encode([
            'errors' => $this->action->getErrors(),
            'result' => $this->action->getResult()
        ]));
    }

    private function redirect()
    {
        $redirect = $this->getRedirect();
        if ($redirect !== null) {
            $transmitter = new ActionTransmitter;
            $transmitter->save($this->action);
            Response::setUrl(Router::toUrlOf($redirect));
        }
    }

    private function getRedirect(): ?string
    {
        $body = $this->action->getBody();
        if (!$this->action->hasErrors()) return $body->getSuccessRedirect();
        else return $body->getFailRedirect();
    }
}