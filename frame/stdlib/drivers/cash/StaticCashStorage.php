<?php namespace frame\stdlib\drivers\cash;

use frame\cash\CashStorage;

class StaticCashStorage extends CashStorage
{
    private $storage = [];

    public function getValue(string $key)
    {
        return $this->storage[$key] ?? null;
    }

    public function isCashed(string $key): bool
    {
        return array_key_exists($key, $this->storage);
    }

    public function cash(string $key, $value)
    {
        $this->storage[$key] = $value;
    }
}