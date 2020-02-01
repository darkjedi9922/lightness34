<?php namespace frame\tools;

class Debug
{
    /**
     * @var float начальная точка времени
     */
    private $start = 0;

    /**
     * Выводит print_r или var_dump переменной, 
     * после чего полностью завершает скрипт.
     * @param mixed $value
     */
    public static function dump($value)
    {
        if (is_array($value)) print_r($value);
        else var_dump($value);
        exit;
    }

    /**
     * Returns an array of two strings:
     * 1 - string representation of the variable (if it 
     * can be simply converted to it) or its type;
     * 2 - string representation of type of the variable.
     */
    public static function getStringAndType($var): array
    {
        if (is_object($var)) {
            $reflection = new \ReflectionObject($var);
            if ($reflection->isAnonymous()) return ['anonymous', 'object'];
            return [get_class($var), 'object'];
        } else if (is_array($var)) return ['array', 'array'];
        else if (is_bool($var)) return [$var ? 'true' : 'false', 'boolean'];
        else if (is_null($var)) return ['null', 'null'];
        else return [strval($var), gettype($var)];
    }

    /**
     * Устанавливает начальную точку времени
     */
    public function setStart()
    {
        $this->start = microtime(true);
    }
    
    /**
     * Показывает разницу между текущим временем и заданной начальной точкой
     */
    public function echoTime()
    {
        $time = microtime(true) - $this->start;
        printf('Скрипт выполнялся %.4F сек.', $time);
    }
}