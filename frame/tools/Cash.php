<?php namespace frame\tools;

/**
 * Не используй этот механизм во frame и engine классах и тогда классы будет проще 
 * тестировать, и их архитектура будет лучше.
 */
abstract class Cash
{
    // В наследнике нужно определить метод, подобный тому, что приведен ниже.
    // При этом метод должен использовать self::cash() для, собственно, кеширования.
    // public static function get(<args>): <type>;

    /**
     * @param string $key Простой строковый ключ, используемый только в пределах
     * наследника. Позволяет хранить разные объекты, основываясь на параметрах в
     * self::get(). Замечание: не передавай пустую строку, она не поддерживается
     * php как ключ массива.
     * @param \callable $creator Должен возвращать создаваемое значение.
     * 
     * Возвращает значение, которое было возвращено вызовом $creator().
     */
    protected static function cash(string $key, callable $creator)
    {
        return self::$storage[$key] ?? 
            self::$storage[$key] = $creator();
    }
    
    private static $storage = [];
}