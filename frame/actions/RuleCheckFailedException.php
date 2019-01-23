<?php namespace frame\actions;

use frame\actions\Action;

/**
 * Исключение выбрасывается во время проверки экшна через конфиг правил проверок
 * на проверке которая провалилась при условии, если имя этой проверки задано
 * в том же конфиге в массиве правила errorRules.
 */
class RuleCheckFailedException extends \Exception
{
    /**
     * @param Action $action Экшн, в котором провалилась проверка.
     * @param string $field Имя поля, на котором провалилась проверка.
     * @param string $rule Проверка, которая провалилась.
     */
    public function __construct($action, $field, $rule)
    {
        $this->action = $action;
        $this->field = $field;
        $this->rule = $rule;
    }

    /**
     * @return Action
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * @return string
     */
    public function getField()
    {
        return $this->field;
    }

    /**
     * @return string
     */
    public function getRule()
    {
        return $this->rule;
    }

    /**
     * @var Action
     */
    private $action;

    /**
     * @var string
     */
    private $field;

    /**
     * @var string
     */
    private $rule;
}