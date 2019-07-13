<?php namespace frame\rules;

use frame\route\Router;
use frame\errors\HttpError;

class RouteRules extends Rules
{
    private $values;
    private $rules;
    private $interData = [];

    public function __construct(Router $router, array $rules)
    {
        $this->setRouter($router);
        $this->rules = $rules;
    }

    public function setRouter(Router $router)
    {
        $this->values = $router->args;
    }

    public function getValues(): array
    {
        return $this->values;
    }

    public function assert()
    {
        $this->interData = [];

        // Проходимся по каждому полю
        foreach ($this->rules as $field => $rules) {
            $this->interData[$field] = [];

            // Правил может не быть.
            if (!isset($rules['rules'])) continue;

            $value = $this->values[$field] ?? null;
            $result = new RuleResult;

            // Проходимся по каждому правилу проверок поля
            foreach ($rules['rules'] as $rule => $ruleValue) {
                $check = $this->getRuleCallback($rule);

                // Т.к. для всей цепочки проверок правила используется один и тот
                // же экземпляр класса, перед каждой обработкой необходимо
                // восстанавливать результат после предыдущей обработки.
                $result->restoreResult();
                /** @var RuleResult|null $result */
                $result = $check($ruleValue, $value, $result);

                // Каждая проверка должна вернуть результат с одним из двух
                // состояний: провал и успех.
                if (!$result || !$result->hasResult())
                    throw new RuleRuntimeException($this, $field, $rule,
                        'Rule result state has not changed.');

                if ($result->isFail()) throw new HttpError(HttpError::NOT_FOUND);
                if ($result->isStopped()) break;
            }

            $this->interData[$field] = $result->getInterDataAll();
        }
    }

    /** @return mixed|null */
    public function getInterData(string $field, string $data)
    {
        return $this->interData[$field][$data] ?? null;
    }
}