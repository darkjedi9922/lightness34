<?php namespace frame\actions\errors;

use frame\actions\Action;

class RuleRuntimeException extends RuleException
{
    /**
     * @param Action $action
     * @param string $type Тип проверяемого значения.
     * @param string $field Имя значения.
     * @param string $rule Имя проверки.
     * @param string $message
     * @param \Throwable $previous
     */
    public function __construct($action, $type, $field, $rule, $message = '', 
        $previous = null) 
    {
        parent::__construct($rule, $message, $previous);
        $this->action = $action;
        $this->type = $type;
        $this->field = $field;
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
}