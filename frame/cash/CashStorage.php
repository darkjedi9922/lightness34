<?php namespace frame\cash;

use frame\core\Driver;

abstract class CashStorage extends Driver
{
    /** @return mixed */
    public abstract function getValue(string $key);
    public abstract function setValue(string $key, $value);
    public abstract function isCashed(string $key): bool;
    
    /** @return mixed */
    public abstract function cash(string $key, callable $creator);
}