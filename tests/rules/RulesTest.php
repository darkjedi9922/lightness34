<?php

use PHPUnit\Framework\TestCase;
use frame\rules\Rules;
use frame\rules\errors\NoRuleException;

class RulesTest extends TestCase
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

    public function testGivesRuleCallbackFromRuleDirectory()
    {
        $rules = new Rules;
        $this->assertIsCallable($rules->getRuleCallback($this->mandatory));
    }

    public function testGivesRuleCallbackFromDirectedSet()
    {
        $rules = new Rules;
        $rule = function() {};
        $rules->setRuleCallback('my-rule', $rule);
        $this->assertEquals($rule, $rules->getRuleCallback('my-rule'));        
    }

    public function testDirectlySetRuleHasMorePriorityThanLoaded()
    {
        $rules = new Rules;
        $rule = function() {};
        $rules->setRuleCallback($this->mandatory, $rule);
        $this->assertEquals($rule, $rules->getRuleCallback($this->mandatory));
    }

    public function testThrowsNoRuleExceptionIfRuleIsNotFoundAtAll()
    {
        $this->expectException(NoRuleException::class);
        $rules = new Rules;
        $rules->getRuleCallback('non/existence/rule');
    }

    public function testValidatesAndSavesTheErrors()
    {
        $rules = new Rules(['login' => 'mortal'], [
            'login' => [
                'rules' => [
                    'base/equals' => 'admin'
                ]
            ]
        ]);

        $rules->validate();

        $this->assertTrue($rules->hasError('login', 'base/equals'));
    }

    public function testHasNoInterDataBeforeValidation()
    {
        $rules = new Rules;
        $this->assertNull($rules->getInterData('login', 'user'));
    }
}