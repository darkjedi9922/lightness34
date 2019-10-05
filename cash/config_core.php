<?php namespace cash;

use frame\tools\Cash;
use frame\config\Config;
use frame\config\DefaultedConfig;
use frame\config\Json;

class config_core extends Cash
{
    public static function get(): Config
    {
        return self::cash(function() {
            return new DefaultedConfig(
                new Json('config/core.json'),
                new Json('config/default/core.json')
            );
        });
    }
}