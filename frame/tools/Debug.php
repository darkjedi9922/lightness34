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