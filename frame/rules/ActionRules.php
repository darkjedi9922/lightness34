<?php namespace frame\rules;

use frame\rules\Rules;
use frame\rules\RuleResult;
use frame\rules\errors\RuleCheckFailedException;

class ActionRules extends Rules
{
    public function validate()
    {
        $validation = $this->getValidation();
        foreach ($validation as $result) {
            /** @var RuleResult $result */
            if ($result->isFail() && $this->isErrorRule($result)) {
                throw new RuleCheckFailedException(
                    $this, $result->getFieldName(), $result->getRuleName());
            }
        }
    }

    private function isErrorRule(RuleResult $result)
    {
        $rules = $this->getRules();
        $field = $result->getFieldName();
        $rule = $result->getRuleName();
        if (isset($rules[$field]['errorRules']) 
            && in_array($rule, $rules[$field]['errorRules']))
        {
            throw new RuleCheckFailedException($this, $field, $rule);
        }
    }
}