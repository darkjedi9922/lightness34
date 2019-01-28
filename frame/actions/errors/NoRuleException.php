<?php namespace frame\actions\errors;

class NoRuleException extends RuleException
{
    /**
     * @param string $rule
     * @param string $message
     * @param \Throwable $previous
     */
    public function __construct($rule, $message = '', $previous = null)
    {
        parent::__construct($rule, $message, $previous);
    }
}