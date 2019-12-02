<?php namespace tests\stubs;

use frame\database\Identity;

class IdentityStub extends Identity
{
    public static function getTable(): string
    {
        return '';
    }
}