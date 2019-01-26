<?php namespace frame\actions;

use frame\actions\Action;

/**
 * Исключение выбрасывается во время проверки экшна через конфиг правил проверок
 * на проверке которая провалилась при условии, если имя этой проверки задано
 * в том же конфиге в массиве правила errorRules.
 * 
 * Это нужно, когда ошибку требуется обрабатывать со стороны PHP, а не сохранять
 * в массив ошибок.
 */
class RuleCheckFailedException extends \Exception
{
    /**
     * @param Action $action Экшн, в котором провалилась проверка.
     * @param string $type Тип значения.
     * @param string $field Имя поля, на котором провалилась проверка.
     * @param string $rule Проверка, которая провалилась.
     */
    public function __construct($action, $type, $field, $rule)
    {
        $this->action = $action;
        $this->type = $type;
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
    public function getType()
    {
        return $this->type;
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
    private $type;

    /**
     * @var string
     */
    private $field;

    /**
     * @var string
     */
    private $rule;
}