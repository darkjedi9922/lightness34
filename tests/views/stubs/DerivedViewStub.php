<?php namespace tests\views\stubs;

class DerivedViewStub extends \frame\views\View
{
    public static function getNamespace(): string
    {
        return 'derived';
    }
}