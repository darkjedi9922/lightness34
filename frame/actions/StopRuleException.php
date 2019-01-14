<?php namespace frame\actions;

class StopRuleException extends \Exception
{
    /**
     * @param bool $ruleCheckResult Результат проверки правила экшна.
     * @param \Throwable $previous
     */
    public function __construct($ruleCheckResult, $previous = null)
    {
        parent::__construct('', 0, $previous);
        $this->ruleCheckResult = $ruleCheckResult;
    }

    /**
     * @return bool Завершилась ли проверка успехом.
     */
    public function isSuccess()
    {
        return $this->ruleCheckResult;
    }

    /**
     * @return bool Завершилась ли проверка неудачей.
     */
    public function isFail()
    {
        return !$this->ruleCheckResult;
    }

    /**
     * @var bool
     */
    private $ruleCheckResult;
}