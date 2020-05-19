<?php namespace engine\statistics\tools;

use frame\cash\StaticCashStorage;
use frame\events\Events;
use frame\tools\Debug;

class StatCashStorage extends StaticCashStorage
{
    const EVENT_CASH_CALL = 'storage-cash-call';

    private $storage;

    public function __construct(StaticCashStorage $storage)
    {
        $this->storage = $storage;
    }

    public function cash(string $key, callable $creator)
    {
        $value = $this->storage->cash($key, $creator);
        list($strRepr, $type) = Debug::getStringAndType($value);
        if ($type === 'object') $type = $strRepr;
        Events::getDriver()->emit(self::EVENT_CASH_CALL, $type, $key, $creator);
        return $value;
    }
}