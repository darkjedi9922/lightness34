<?php

use PHPUnit\Framework\TestCase;
use frame\actions\rules\ActionBaseRules;
use frame\actions\RuleResult;

class ActionRulesTest extends TestCase
{
    public function testMandatoryRuleReturnsSuccessIfRuleIsTrueAndValueIsNotNull()
    {
        $rules = new ActionBaseRules;
        $mandatory = $rules->getMandatoryRule();

        $result = $mandatory(true, 'some-value', new RuleResult);

        $this->assertTrue($result->isSuccess());
    }

    public function testMandatoryRuleReturnsSuccessIfRuleIsFalseAndValueIsNotNull()
    {
        $rules = new ActionBaseRules;
        $mandatory = $rules->getMandatoryRule();

        $result = $mandatory(false, 'some-value', new RuleResult);

        $this->assertTrue($result->isSuccess());
    }

    public function testMandatoryRuleReturnsSuccessIfRuleIsFalseAndValueIsNull()
    {
        $rules = new ActionBaseRules;
        $mandatory = $rules->getMandatoryRule();

        $result = $mandatory(false, null, new RuleResult);

        $this->assertTrue($result->isSuccess());
    }

    public function testMandatoryRuleReturnsFailIfRuleIsTrueAndValueIsNull()
    {
        $rules = new ActionBaseRules;
        $mandatory = $rules->getMandatoryRule();

        $result = $mandatory(true, null, new RuleResult);

        $this->assertTrue($result->isFail());
    }

    public function testEmptinessRuleReturnsSuccessIfRuleIsFalseAndValueIsNotEmpty()
    {
        $rules = new ActionBaseRules;
        $emptiness = $rules->getEmptinessRule();

        $result = $emptiness(false, 'some-value', new RuleResult);

        $this->assertTrue($result->isSuccess());
    }

    public function testEmptinessRuleReturnsSuccessIfRuleIsTrueAndValueIsNotEmpty()
    {
        $rules = new ActionBaseRules;
        $emptiness = $rules->getEmptinessRule();

        $result = $emptiness(true, 'some-value', new RuleResult);

        $this->assertTrue($result->isSuccess());
    }

    public function testEmptinessRuleReturnsSuccessIfRuleIsTrueAndValueIsEmpty()
    {
        $rules = new ActionBaseRules;
        $emptiness = $rules->getEmptinessRule();

        $result = $emptiness(true, '', new RuleResult);

        $this->assertTrue($result->isSuccess());
    }

    public function testEmptinessRuleReturnsFailIfRuleIsFalseAndValueIsEmpty()
    {
        $rules = new ActionBaseRules;
        $emptiness = $rules->getEmptinessRule();

        $result = $emptiness(false, '', new RuleResult);

        $this->assertTrue($result->isFail());
    }

    public function testMinLengthRuleReturnsSuccessIfValueHasThisMinLength()
    {
        $rules = new ActionBaseRules;
        $minLength = $rules->getMinLengthRule();

        $this->assertTrue($minLength(5, '12345', new RuleResult)->isSuccess());
        $this->assertTrue($minLength(5, '123456', new RuleResult)->isSuccess());
    }

    public function testMinLengthRuleReturnsFailIfValueDoesNotHaveThisMinLength()
    {
        $rules = new ActionBaseRules;
        $minLength = $rules->getMinLengthRule();

        $this->assertTrue($minLength(5, '1234', new RuleResult)->isFail());
    }

    public function testMaxLengthRuleReturnsSuccessIfValueLengthDoesNotMoreThanThat()
    {
        $rules = new ActionBaseRules;
        $maxLength = $rules->getMaxLengthRule();

        $this->assertTrue($maxLength(5, '1234', new RuleResult)->isSuccess());
        $this->assertTrue($maxLength(5, '12345', new RuleResult)->isSuccess());
    }

    public function testMaxLengthRuleReturnsFailIfValueLengthIsMoreThanThat()
    {
        $rules = new ActionBaseRules;
        $maxLength = $rules->getMaxLengthRule();

        $this->assertTrue($maxLength(5, '123456', new RuleResult)->isFail());
    }

    public function testRegexpRuleReturnsSuccessIfValueMatchesToRegexp()
    {
        $rules = new ActionBaseRules;
        $regexp = $rules->getRegexpRule();

        $this->assertTrue($regexp('/Abc/i', 'The abc', new RuleResult)->isSuccess());
    }

    public function testRegexpRuleReturnsFailIfValueDoesNotMatchToRegexp()
    {
        $rules = new ActionBaseRules;
        $regexp = $rules->getRegexpRule();

        $this->assertTrue($regexp('/Abc/i', 'The efg', new RuleResult)->isFail());
    }
}