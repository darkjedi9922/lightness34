<?php namespace frame\cash;

use frame\core\Driver;

abstract class CashStorage extends Driver
{
    /** @return mxixed */
    public abstract function getValue(string $key);
    public abstract function isCashed(string $key): bool;
    public abstract function cash(string $key, $value);
}