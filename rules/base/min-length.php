<?php

use frame\rules\RuleResult;

/**
 * Проверяет минимальную длинну поля (с заданным int значением).
 */
return function (int $rule, string $value, RuleResult $result): RuleResult {
    $isOk = mb_strlen($value) >= $rule;
    return $result->result($isOk);
};