<?php
use frame\rules\RuleResult;

return function(string $rule, string $value, RuleResult $result): RuleResult {
    return $result->result($value == $rule);
};