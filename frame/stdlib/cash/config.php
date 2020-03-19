<?php namespace frame\stdlib\cash;

use frame\tools\Cash;
use frame\config\Config as FrameConfig;
use frame\config\Json;

class config extends Cash
{
    public static function get(string $name): FrameConfig
    {
        return self::cash($name, function() use ($name) {
            return new Json(ROOT_DIR . "/config/$name.json");
        });
    }
}