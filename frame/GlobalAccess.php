<?php namespace frame;

class GlobalAccess
{
    private static $storage = [];

    public function global(string $name)
    {
        return self::$storage[$name] ?? self::$storage[$name] =
            require ROOT_DIR . '/globals/' . $name . '.php';
    }
}