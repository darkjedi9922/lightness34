<?php namespace tests\views\stubs;

class DerivedViewStub extends ViewStub
{
    public static function getNamespace(): string
    {
        return 'derived';
    }
}