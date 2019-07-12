<?php

use frame\rules\RuleResult;

/**
 * Проверяет на совпадение значения с регулярным выражением.
 */
return function (string $rule, string $value, RuleResult $result): RuleResult {
    return $result->result(preg_match($rule, $value));
};