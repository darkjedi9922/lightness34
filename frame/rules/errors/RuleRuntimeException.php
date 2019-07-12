<?php namespace frame\rules\errors;

use frame\rules\Rules;

class RuleRuntimeException extends RuleException
{
    private $rules;
    private $field;

    public function __construct(
        Rules $rules, 
        string $field, 
        string $rule, 
        string $message = '', 
        ?\Throwable $previous = null) 
    {
        parent::__construct($rule, $message, $previous);
        $this->rules = $rules;
        $this->field = $field;
    }

    public function getRules(): Rules
    {
        return $this->rules;
    }

    public function getField(): string
    {
        return $this->field;
    }
}