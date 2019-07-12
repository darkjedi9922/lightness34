<?php namespace frame\rules\errors;

class RuleException extends \Exception
{
    public function __construct(string $rule, string $message = '', ?\Throwable $previous = null)
    {
        parent::__construct($message, 0, $previous);
        $this->rule = $rule;
    }

    public function getRule(): string
    {
        return $this->rule;
    }

    private $rule;
}