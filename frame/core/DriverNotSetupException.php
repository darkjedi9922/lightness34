<?php namespace frame\core;

use Exception;
use function lightlib\remove_prefix;

class DriverNotSetupException extends Exception
{
    private $driverClass;
    private $callerFile;
    private $callerLine;

    public function __construct(string $requiredDriverClass)
    {
        $this->driverClass = $requiredDriverClass;
        
        $backtrace = debug_backtrace();
        $caller = ($backtrace[2]['class'] ?? null) === Driver::class
            ? $backtrace[2] : $backtrace[1];
        
        $this->callerFile = ltrim(remove_prefix($caller['file'], ROOT_DIR), '\\/');
        $this->callerLine = $caller['line'];
        
        parent::__construct(
            "File \"{$this->callerFile}\" on line {$this->callerLine} requires" .
            " driver \"$requiredDriverClass\" but it is not setup before it." .
            " You need to setup the driver before this call in the index.php."
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