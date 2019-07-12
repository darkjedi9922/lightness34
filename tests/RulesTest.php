<?php

use PHPUnit\Framework\TestCase;
use frame\rules\Rules;

class RuleTest extends TestCase
{
    protected $mandatory = 'base/mandatory';
    protected $noExistence = 'non/existence/rule';

    public function testLoadRulesFromRuleDirectory()
    {
        $rule = Rules::loadRule($this->mandatory);
        $this->assertIsCallable($rule);
    }

    public function testReturnsNullIfRuleIsNotFoundDuringLoading()
    {
        $rule = Rules::loadRule($this->noExistence);
        $this->assertNull($rule);
    }
}