<?php namespace frame\core;

use Throwable;
use Exception;
use function lightlib\remove_prefix;

class DriverNotSetupException extends Exception
{
    private $driverClass;
    private $callerFile;
    private $callerLine;

    public function __construct(
        string $requiredDriverClass,
        string $callerFile,
        int $callerLine,
        ?Throwable $previous = null
    ) {
        $this->driverClass = $requiredDriverClass;
        $this->callerFile = $callerFile;
        $this->callerLine = $callerLine;
        
        parent::__construct(
            "File \"{$callerFile}\" on line {$callerLine} requires abstract driver" .
            " \"$requiredDriverClass\" but it is not replaced by instantiable" .
            " class before it. You need to replace the driver before this call in" .
            " the index.php.",
            0, $previous
        );
    }

    public function getRequiredDriverClass(): string
    {
        return $this->driverClass;
    }

    public function getCallerFile(): string
    {
        return $this->callerFile;
    }

    public function getCallerLine(): int
    {
        return $this->callerLine;
    }
}