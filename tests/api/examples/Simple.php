<?php namespace tests\api\examples;

use frame\api\Api;

class Simple extends Api
{
    public static $expectedResult = 'All OK';

    public function exec()
    {
        return static::$expectedResult;
    }
}