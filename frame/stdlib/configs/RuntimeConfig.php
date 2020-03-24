<?php namespace frame\stdlib\configs;

use frame\config\Config;

class RuntimeConfig extends Config
{
    protected function loadConfig(): array
    {
        return [];
    }

    protected function saveConfig()
    {
        // Runtime конфиг уже сохраняется в приватном массиве с данными.
        // Для выброса исключения это недостаточно исключительная ситуация, так как
        // все таки он сохраняется - результат достижим.
    }
}