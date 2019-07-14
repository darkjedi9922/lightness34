<?php

use PHPUnit\Framework\TestCase;
use frame\rules\RuleResult;
use frame\rules\Rules;

class BaseRulesTest extends TestCase
{
    public function testMandatoryRuleSucceedsIfRuleIsTrueAndValueIsNotNull()
    {
        $mandatory = Rules::loadRule('base/mandatory');
        $result = $mandatory(true, 'some-value', new RuleResult);
        $this->assertTrue($result->isSuccess());
    }

    public function testMandatoryRuleSucceedsIfRuleIsFalseAndValueIsNotNull()
    {
        $mandatory = Rules::loadRule('base/mandatory');
        $result = $mandatory(false, 'some-value', new RuleResult);
        $this->assertTrue($result->isSuccess());
    }

    public function testMandatoryRuleSucceedsIfRuleIsFalseAndValueIsNull()
    {
        $mandatory = Rules::loadRule('base/mandatory');
        $result = $mandatory(false, null, new RuleResult);
        $this->assertTrue($result->isSuccess());
    }

    public function testMandatoryRuleFailsIfRuleIsTrueAndValueIsNull()
    {
        $mandatory = Rules::loadRule('base/mandatory');
        $result = $mandatory(true, null, new RuleResult);
        $this->assertTrue($result->isFail());
    }

    public function testEmptinessRuleSucceedsIfRuleIsFalseAndValueIsNotEmpty()
    {
        $emptiness = Rules::loadRule('base/emptiness');
        $result = $emptiness(false, 'some-value', new RuleResult);
        $this->assertTrue($result->isSuccess());
    }

    public function testEmptinessRuleSucceedsIfRuleIsTrueAndValueIsNotEmpty()
    {
        $emptiness = Rules::loadRule('base/emptiness');
        $result = $emptiness(true, 'some-value', new RuleResult);
        $this->assertTrue($result->isSuccess());
    }

    public function testEmptinessRuleSucceedsIfRuleIsTrueAndValueIsEmpty()
    {
        $emptiness = Rules::loadRule('base/emptiness');
        $result = $emptiness(true, '', new RuleResult);
        $this->assertTrue($result->isSuccess());
    }

    public function testEmptinessRuleFailsIfRuleIsFalseAndValueIsEmpty()
    {
        $emptiness = Rules::loadRule('base/emptiness');
        $result = $emptiness(false, '', new RuleResult);
        $this->assertTrue($result->isFail());
    }

    public function testMinLengthRuleSucceedsIfValueHasThisMinLength()
    {
        $minLength = Rules::loadRule('base/min-length');
        $this->assertTrue($minLength(5, '12345', new RuleResult)->isSuccess());
        $this->assertTrue($minLength(5, '123456', new RuleResult)->isSuccess());
    }

    public function testMinLengthRuleFailsIfValueDoesNotHaveThisMinLength()
    {
        $minLength = Rules::loadRule('base/min-length');
        $this->assertTrue($minLength(5, '1234', new RuleResult)->isFail());
    }

    public function testMaxLengthRuleSucceedsIfValueLengthDoesNotMoreThanThat()
    {
        $maxLength = Rules::loadRule('base/max-length');
        $this->assertTrue($maxLength(5, '1234', new RuleResult)->isSuccess());
        $this->assertTrue($maxLength(5, '12345', new RuleResult)->isSuccess());
    }

    public function testMaxLengthRuleFailsIfValueLengthIsMoreThanThat()
    {
        $maxLength = Rules::loadRule('base/max-length');
        $this->assertTrue($maxLength(5, '123456', new RuleResult)->isFail());
    }

    public function testRegexpRuleSucceedsIfValueMatchesToRegexp()
    {
        $regexp = Rules::loadRule('base/regexp');
        $this->assertTrue($regexp('/Abc/i', 'The abc', new RuleResult)->isSuccess());
    }

    public function testRegexpRuleFailsIfValueDoesNotMatchToRegexp()
    {
        $regexp = Rules::loadRule('base/regexp');
        $this->assertTrue($regexp('/Abc/i', 'The efg', new RuleResult)->isFail());
    }

    public function testEqualsRuleSucceedsIfTheValueEqualsTheRule()
    {
        $equals = Rules::loadRule('base/equals');
        $result = $equals('correct', 'correct', new RuleResult);
        $this->assertTrue($result->isSuccess());
    }

    public function testEqualsRuleFailsIfTheValueDoesNotEqualTheRule()
    {
        $equals = Rules::loadRule('base/equals');
        $result = $equals('correct', 'wrong', new RuleResult);
        $this->assertTrue($result->isFail());
    }
}