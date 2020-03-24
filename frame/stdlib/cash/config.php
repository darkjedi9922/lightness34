<?php namespace frame\stdlib\cash;

use frame\tools\Cash;
use frame\config\ConfigRouter;
use frame\config\NamedConfig;

class config extends Cash
{
    public static function get(string $name): NamedConfig
    {
        return self::cash($name, function() use ($name) {
            return ConfigRouter::getDriver()->findConfig($name);
        });
    }
}