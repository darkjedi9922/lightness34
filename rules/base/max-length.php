<?php

use frame\actions\RuleResult;

/**
 * Проверяет максимальную длинну поля (с заданным int значением).
 */
return function (int $rule, string $value, RuleResult $result): RuleResult {
    $isOk = mb_strlen($value) <= $rule;
    return $result->result($isOk);
};