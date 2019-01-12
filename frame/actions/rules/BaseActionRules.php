<?php namespace frame\actions\rules;

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
        return function($rule, $value) {
            if ($rule == true && $value === null) return false;
            return true;
        };
    }

    /**
     * Разрешено ли пустое значение в поле (true|false).
     * @return \callable
     */
    public function getEmptinessRule()
    {
        return function($rule, $value) {
            if ($rule == false && !$value) return false;
            return true;
        };
    }
}