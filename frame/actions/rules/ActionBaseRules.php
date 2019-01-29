<?php namespace frame\actions\rules;

use frame\actions\RuleResult;

/**
 * Методы класса возвращают callback-функции для установки как rule в Action.
 * @see \frame\actions\Action::setRule
 */
class ActionBaseRules
{
    /**
     * Обязательно ли поле для передачи (true|false).
     * Если поля нет, завершает цепочку обработчиков, независимо от результата 
     * проверки.
     * @return \callable
     */
    public function getMandatoryRule()
    {
        /**
         * @param bool $rule
         * @param mixed $value
         * @param RuleResult $result
         */
        return function($rule, $value, $result) {
            if ($value !== null) return $result->succeed();
            return $result->result(!$rule)->stop();
        };
    }

    /**
     * Разрешено ли пустое значение в поле (true|false).
     * Если значение пусто, завершает цепочку обработчиков, независимо от результата
     * проверки.
     * @return \callable
     */
    public function getEmptinessRule()
    {
        /**
         * @param bool $rule
         * @param mixed $value
         * @param RuleResult $result
         */
        return function($rule, $value, $result) {
            if ($value) return $result->succeed();
            return $result->result($rule)->stop();
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
         * @param RuleResult $result
         * @return bool
         */
        return function($rule, $value, $result) {
            $isOk = mb_strlen($value) >= $rule;
            return $result->result($isOk);
        };
    }

    /**
     * Проверяет максимальную длинну поля (с заданным int значением).
     * @return \callable
     */
    public function getMaxLengthRule()
    {
        /**
         * @param int $rule
         * @param string $value
         * @param RuleResult $result
         * @return bool
         */
        return function ($rule, $value, $result) {
            $isOk = mb_strlen($value) <= $rule;
            return $result->result($isOk);
        };
    }

    /**
     * Проверяет на совпадение значения с регулярным выражением.
     * @return \callable
     */
    public function getRegexpRule()
    {
        /**
         * @param string $rule
         * @param string $value
         * @param RuleResult $result
         * @return bool
         */
        return function($rule, $value, $result) {
            return $result->result(preg_match($rule, $value));
        };
    }
}