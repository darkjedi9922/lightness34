<?php
use PHPUnit\Framework\TestCase;
use frame\actions\fields\CheckboxGroup;

class CheckboxGroupTest extends TestCase
{
    public function testGivesAllCheckedValues()
    {
        $checkedValues = ['a', 'c'];
        $field = new CheckboxGroup($checkedValues);
        $this->assertEquals($checkedValues, $field->getCheckedValues());
    }
}