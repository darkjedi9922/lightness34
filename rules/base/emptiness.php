<?php

use frame\actions\RuleResult;

/**
 * Разрешено ли пустое значение в поле (true|false).
 * Если значение пусто, завершает цепочку обработчиков, независимо от результата
 * проверки.
 */
return function (bool $rule, $value, RuleResult $result): RuleResult {
    if ($value) return $result->succeed();
    return $result->result($rule)->stop();
};