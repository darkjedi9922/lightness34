<?php namespace frame\rules;

use frame\route\Router;
use frame\errors\HttpError;
use frame\rules\RuleResult;

class RouteRules extends Rules
{
    public function __construct(Router $router, array $rules)
    {
        parent::__construct([], $rules);
        $this->setRouter($router);
    }

    public function setRouter(Router $router)
    {
        $this->setValues($router->args);
    }

    public function assert()
    {
        foreach ($this->getValidation() as $result) {
            /** @var RuleResult $result */
            if ($result->isFail()) throw new HttpError(HttpError::NOT_FOUND);
        }
    }
}