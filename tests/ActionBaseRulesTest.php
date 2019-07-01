<?php

use PHPUnit\Framework\TestCase;
use frame\actions\RuleResult;
use frame\actions\Action;

class ActionBaseRulesTest extends TestCase
{
    public function testMandatoryRuleReturnsSuccessIfRuleIsTrueAndValueIsNotNull()
    {
        $mandatory = Action::loadRule('base/mandatory');
        $result = $mandatory(true, 'some-value', new RuleResult);
        $this->assertTrue($result->isSuccess());
    }

    public function testMandatoryRuleReturnsSuccessIfRuleIsFalseAndValueIsNotNull()
    {
        $mandatory = Action::loadRule('base/mandatory');
        $result = $mandatory(false, 'some-value', new RuleResult);
        $this->assertTrue($result->isSuccess());
    }

    public function testMandatoryRuleReturnsSuccessIfRuleIsFalseAndValueIsNull()
    {
        $mandatory = Action::loadRule('base/mandatory');
        $result = $mandatory(false, null, new RuleResult);
        $this->assertTrue($result->isSuccess());
    }

    public function testMandatoryRuleReturnsFailIfRuleIsTrueAndValueIsNull()
    {
        $mandatory = Action::loadRule('base/mandatory');
        $result = $mandatory(true, null, new RuleResult);
        $this->assertTrue($result->isFail());
    }

    public function testEmptinessRuleReturnsSuccessIfRuleIsFalseAndValueIsNotEmpty()
    {
        $emptiness = Action::loadRule('base/emptiness');
        $result = $emptiness(false, 'some-value', new RuleResult);
        $this->assertTrue($result->isSuccess());
    }

    public function testEmptinessRuleReturnsSuccessIfRuleIsTrueAndValueIsNotEmpty()
    {
        $emptiness = Action::loadRule('base/emptiness');
        $result = $emptiness(true, 'some-value', new RuleResult);
        $this->assertTrue($result->isSuccess());
    }

    public function testEmptinessRuleReturnsSuccessIfRuleIsTrueAndValueIsEmpty()
    {
        $emptiness = Action::loadRule('base/emptiness');
        $result = $emptiness(true, '', new RuleResult);
        $this->assertTrue($result->isSuccess());
    }

    public function testEmptinessRuleReturnsFailIfRuleIsFalseAndValueIsEmpty()
    {
        $emptiness = Action::loadRule('base/emptiness');
        $result = $emptiness(false, '', new RuleResult);
        $this->assertTrue($result->isFail());
    }

    public function testMinLengthRuleReturnsSuccessIfValueHasThisMinLength()
    {
        $minLength = Action::loadRule('base/min-length');
        $this->assertTrue($minLength(5, '12345', new RuleResult)->isSuccess());
        $this->assertTrue($minLength(5, '123456', new RuleResult)->isSuccess());
    }

    public function testMinLengthRuleReturnsFailIfValueDoesNotHaveThisMinLength()
    {
        $minLength = Action::loadRule('base/min-length');
        $this->assertTrue($minLength(5, '1234', new RuleResult)->isFail());
    }

    public function testMaxLengthRuleReturnsSuccessIfValueLengthDoesNotMoreThanThat()
    {
        $maxLength = Action::loadRule('base/max-length');
        $this->assertTrue($maxLength(5, '1234', new RuleResult)->isSuccess());
        $this->assertTrue($maxLength(5, '12345', new RuleResult)->isSuccess());
    }

    public function testMaxLengthRuleReturnsFailIfValueLengthIsMoreThanThat()
    {
        $maxLength = Action::loadRule('base/max-length');
        $this->assertTrue($maxLength(5, '123456', new RuleResult)->isFail());
    }

    public function testRegexpRuleReturnsSuccessIfValueMatchesToRegexp()
    {
        $regexp = Action::loadRule('base/regexp');
        $this->assertTrue($regexp('/Abc/i', 'The abc', new RuleResult)->isSuccess());
    }

    public function testRegexpRuleReturnsFailIfValueDoesNotMatchToRegexp()
    {
        $regexp = Action::loadRule('base/regexp');
        $this->assertTrue($regexp('/Abc/i', 'The efg', new RuleResult)->isFail());
    }
}