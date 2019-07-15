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

    public function testLoadsTheSameRuleOnlyOnce()
    {
        $firstRule = Rules::loadRule($this->mandatory);
        $secondRule = Rules::loadRule($this->mandatory);

        // loadRule мог вернуть null, а тогда конечно null == null. Нужно убедиться
        // что это не null. При этом оба значения должны быть одинаковы, значит не
        // нужно проверять оба. 
        $this->assertNotNull($firstRule);
        
        $this->assertEquals($firstRule, $secondRule);
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

    public function testDefaultValue()
    {
        $rules = new Rules([], [
            "alter" => [
                "default" => ["Doctor Who", "TARDIS"],
            ],
            "enemy" => [
                "default" => ["Dalek"]
            ]
        ]);

        $this->assertEquals('Doctor Who', $rules->getDefault('alter', false));
        $this->assertEquals('TARDIS', $rules->getDefault('alter', true));
        $this->assertEquals('Dalek', $rules->getDefault('enemy', false));
        $this->assertEquals('Dalek', $rules->getDefault('enemy', true));
        $this->assertEquals(null, $rules->getDefault('true-name', false));
        $this->assertEquals('', $rules->getDefault('true-name', true));
    }

    public function testGetValueInCombinationWithDefaultRules()
    {
        $rules = new Rules([
            'username' => 'BadUser',
            'empty-field' => '',
            'question' => ''
        ], [
            'answer' => [
                'default' => [42]
            ],
            'question' => [
                'default' => ['...']
            ]
        ]);

        $this->assertEquals('BadUser', $rules->getValue('username'));
        $this->assertEquals('', $rules->getValue('empty-field'));
        $this->assertEquals(null, $rules->getValue('non-existence-field'));
        $this->assertEquals(42, $rules->getValue('answer'));
        $this->assertEquals('...', $rules->getValue('question'));
    }
}