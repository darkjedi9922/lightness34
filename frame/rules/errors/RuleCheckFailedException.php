<?php namespace frame\rules\errors;

use frame\rules\Rules;

/**
 * Исключение выбрасывается во время проверки через конфиг правил проверок
 * на проверке которая провалилась при условии, если имя этой проверки задано
 * в том же конфиге в массиве правила errorRules.
 * 
 * Это нужно, когда ошибку требуется обрабатывать со стороны PHP, а не сохранять
 * в массив ошибок.
 */
class RuleCheckFailedException extends RuleRuntimeException
{
    public function __construct(
        Rules $rules,
        string $field, 
        string $rule,
        string $message = '', 
        ?\Throwable $previous = null) 
    {
        parent::__construct($rules, $field, $rule, $message, $previous);
    }
}