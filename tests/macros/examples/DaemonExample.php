<?php namespace tests\macros\examples;

use frame\events\DaemonMacro;
use frame\tools\files\Directory;

class DaemonExample extends DaemonMacro
{
    public $executeCount = 0;

    public function __destruct()
    {
        Directory::deleteNonEmpty($this->getRuntimeFolder());
    }

    protected function execDaemon()
    {
        $this->executeCount += 1;
    }

    protected function getRuntimeFolder(): string
    {
        return ROOT_DIR . '/tests/macros/runtime/daemons';
    }
}