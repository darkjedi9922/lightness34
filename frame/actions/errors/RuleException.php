<?php namespace frame\actions\errors;

class RuleException extends \Exception
{
    /**
     * @param string $rule
     * @param string $message
     * @param \Throwable $previous
     */
    public function __construct($rule, $message = '', $previous = null)
    {
        parent::__construct($message, 0, $previous);
        $this->rule = $rule;
    }

    /**
     * @return string
     */
    public function getRule()
    {
        return $this->rule;
    }

    /**
     * @var string
     */
    private $rule;
}