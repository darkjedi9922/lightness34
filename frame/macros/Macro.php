<?php namespace frame\macros;

/**
 * Макросы - механизм фреймворка, позволяющий настраивать выполнение различных 
 * действий, активируемых классами с помощью событий.
 * 
 * Объект макроса может использоваться в качестве callable.
 */
abstract class Macro
{
    public abstract function exec(...$args);

    public final function __invoke(...$args)
    {
        $this->exec(...$args);
    }
}