<?php namespace frame\actions\rules;

use frame\actions\StopRuleException;

/**
 * Методы класса возвращают callback-функции для установки как rule в Action.
 * @see \frame\Action::setRule
 */
class BaseActionRules
{
    /**
     * Обязательно ли поле для передачи (true|false).
     * @return \callable
     */
    public function getMandatoryRule()
    {
        /**
         * @param bool $rule
         * @param mixed $value
         * @throws StopRuleException
         */
        return function($rule, $value) {
            if ($value !== null) return true;
            throw new StopRuleException(!$rule);
        };
    }

    /**
     * Разрешено ли пустое значение в поле (true|false).
     * @return \callable
     */
    public function getEmptinessRule()
    {
        /**
         * @param bool $rule
         * @param mixed $value
         * @throws StopRuleException Если поле пустое.
         */
        return function($rule, $value) {
            if ($value) return true;
            throw new StopRuleException($rule);
        };
    }

    /**
     * Проверяет минимальную длинну поля (с заданным int значением).
     * @return \callable
     */
    public function getMinLengthRule()
    {
        /**
         * @param int $rule
         * @param string $value
         * @return bool
         */
        return function($rule, $value) {
            return strlen($value) >= $rule;
        };
    }
}