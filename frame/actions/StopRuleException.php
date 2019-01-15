<?php namespace frame\actions;

/**
 * При выбросе этого исключения в обработчике правила проверки данных экшна, все 
 * оставшиеся правила проверяемого поля не будут обработаны.
 * 
 * Это нужно, когда нет смысла проверять значение поля дальше, например, если
 * значение поля не было передано вообще и тогда проверять дальше нечего.
 */
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