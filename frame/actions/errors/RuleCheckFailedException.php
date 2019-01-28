<?php namespace frame\actions\errors;

use frame\actions\Action;

/**
 * Исключение выбрасывается во время проверки экшна через конфиг правил проверок
 * на проверке которая провалилась при условии, если имя этой проверки задано
 * в том же конфиге в массиве правила errorRules.
 * 
 * Это нужно, когда ошибку требуется обрабатывать со стороны PHP, а не сохранять
 * в массив ошибок.
 */
class RuleCheckFailedException extends RuleRuntimeException
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
        parent::__construct($action, $type, $field, $rule, $message, $previous);
    }
}