<?php

use frame\rules\RuleResult;

/**
 * Обязательно ли поле для передачи (true|false).
 * Если поля нет, завершает цепочку обработчиков, независимо от результата 
 * проверки.
 */
return function(bool $rule, $value, RuleResult $result): RuleResult {
    if ($value !== null) return $result->succeed();
    return $result->result(!$rule)->stop();
};