<?php namespace globals;

use frame\tools\GlobalValue;
use frame\config\Config;
use frame\config\DefaultedConfig;
use frame\config\Json;

class config_core extends GlobalValue
{
    public static function get(): Config
    {
        return parent::get();
    }

    public static function create(): Config
    {
        return new DefaultedConfig(
            new Json('config/core.json'),
            new Json('config/default/core.json')
        );
    }
}