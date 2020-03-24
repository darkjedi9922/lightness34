<?php namespace frame\stdlib\cash;

use frame\tools\Cash;
use frame\stdlib\configs\JsonConfig;

class config extends Cash
{
    public static function get(string $name): JsonConfig
    {
        return self::cash($name, function() use ($name) {
            return new JsonConfig(ROOT_DIR . "/config/$name");
        });
    }
}