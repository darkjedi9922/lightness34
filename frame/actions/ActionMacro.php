<?php namespace frame\actions;

use frame\actions\Action;
use frame\actions\ActionRouter;
use frame\actions\ActionTransmitter;
use frame\route\Response;
use frame\route\Router;
use frame\route\Request;
use frame\route\GetMacro;
use frame\events\Events;

class ActionMacro extends GetMacro
{
    const EVENT_ACTION_TRIGGERED = 'action-triggered';

    /** @var Action */
    private $action;

    protected function triggerExec(string $value)
    {
        Events::getDriver()->emit(self::EVENT_ACTION_TRIGGERED);
        $router = new ActionRouter;
        $route = Router::getDriver()->getCurrentRoute();
        $this->action = $router->fromTriggerUrl($route->url);
        $tokenizer = new ActionToken($this->action);
        $tokenizer->validate();
        $this->action->exec();
        $this->result();
    }
    
    private function result()
    {
        if (Request::getDriver()->isAjax() || ($redirect = $this->getRedirect()) === null) {
            $this->jsonify();
        } else $this->redirect($redirect);
    }

    private function jsonify()
    {
        $redirect = $this->getRedirect();
        Response::getDriver()->setText(json_encode([
            'errors' => $this->action->getErrors(),
            'result' => $this->action->getResult(),
            'redirect' => !$this->isDefaultRedirect($redirect) ? $redirect : null
        ]));
    }

    private function redirect(string $redirect)
    {
        if ($this->isDefaultRedirect($redirect)) {
            $request = Request::getDriver();
            $redirect = $request->getPreviousRequest() ?? '/';
        }
        $transmitter = new ActionTransmitter;
        $transmitter->save($this->action);
        $router = Router::getDriver()->getDriver();
        Response::getDriver()->setUrl($router->makeRoute($redirect));
    }

    private function getRedirect(): ?string
    {
        $body = $this->action->getBody();
        if (!$this->action->hasErrors()) return $body->getSuccessRedirect();
        else return $body->getFailRedirect();
    }

    private function isDefaultRedirect(?string $redirect): bool
    {
        return $redirect === $this->action->getBody()::DEFAULT_REDIRECT;
    }
}