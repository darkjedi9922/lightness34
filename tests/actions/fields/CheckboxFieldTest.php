<?php
use PHPUnit\Framework\TestCase;
use frame\actions\fields\CheckboxField;

class CheckboxFieldTest extends TestCase
{
    public function testIsCheckedIfDefaultHtmlOnValueRecieved()
    {
        $field = new CheckboxField('on');
        $this->assertEquals(true, $field->isChecked());
    }

    public function testIsNotCheckedIfDefaultHtmlValueOnNotRecieved()
    {
        $field = CheckboxField::createDefault();
        $this->assertEquals(false, $field->isChecked());
    }
}