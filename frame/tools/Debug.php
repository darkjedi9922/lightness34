<?php namespace frame\tools;

use Throwable;

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

    public static function getBackTrace(?array $backtrace = null): string
    {
        $trace = array_reverse($backtrace ?? debug_backtrace());
        $result = "#0 {main}\n";
        for ($i = 0, $c = count($trace); $i < $c; ++$i) {
            $number = $i + 1;
            $file = $trace[$i]['file'] ?? null;
            $class = $trace[$i]['class'] ?? null;
            $object = $trace[$i]['object'] ?? null;
            $function = $trace[$i]['function'];
            $callType = $trace[$i]['type'] ?? '';
            $args = $trace[$i]['args'];
            for ($j = 0, $jc = count($args); $j < $jc; ++$j) {
                list($argStr, $argType) = static::getStringAndType($args[$j]);
                $args[$j] = "($argType) $argStr";
            }
            $argsString = implode(', ', $args);
            if ($class) {
                $call = "{$class}{$callType}{$function}($argsString)";
            } else $call = "$function($argsString)";
            if ($file !== null) {
                $line = $trace[$i]['line'] ?? null;
                $result .= "#$number $file($line): $call\n";
            } else $result .= "#$number $call\n";
        }
        return rtrim($result);
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

    public static function getErrorMessage(Throwable $e): string
    {
        $result = '';
        $type = get_class($e);
        $code = $e->getCode();
        $message = $e->getMessage();
        $file = $e->getFile();
        $line = $e->getLine();
        if ($e->getPrevious()) {
            $result .= static::getErrorMessage($e->getPrevious()) . "\nNext: ";
        }
        $result .= "$type #$code: $message in $file($line)";
        $result .= "\nStack trace:\n" . static::getBackTrace($e->getTrace());
        return $result;
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