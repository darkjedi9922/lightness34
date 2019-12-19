<?php namespace frame\macros;

/**
 * Макросы - механизм фреймворка, позволяющий настраивать выполнение различных 
 * действий, активируемых классами с помощью событий.
 */
interface Macro
{
    public function exec(...$args);
}