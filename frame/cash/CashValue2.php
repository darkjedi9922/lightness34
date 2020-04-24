<?php namespace frame\cash;

// use frame\events\Events;

// abstract class CashValue
// {
//     /**
//      * Вызывается во время обращения к значению кеша.
//      * В параметры события передается string класс значения кеша, string ключ, под 
//      * которым он сохраняется и массив переданных аргументов.
//      */
//     const EVENT_CALL = 'cash-call';

//     // В наследнике нужно определить метод, подобный тому, что приведен ниже.
//     // При этом метод должен использовать self::cash() для, собственно, кеширования.
//     // public static function get(<args>): <type>;

//     /** @return mixed */
//     public abstract static function create(...$args);
//     public abstract static function toKey(array $args): string;

//     /** @return mixed */
//     public static function getValue(...$args)
//     {
//         $key = static::toKey($args);
//         Events::getDriver()->emit(self::EVENT_CALL, static::class, $key, $args);
//         if (!array_key_exists($key, self::$storage[static::class] ?? []))
//             self::$storage[static::class][$key] = static::create(...$args);
//         return self::$storage[static::class][$key];
//     }

//     private static $storage = [];
// }