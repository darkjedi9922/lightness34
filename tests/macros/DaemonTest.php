<?php

use PHPUnit\Framework\TestCase;
use tests\macros\examples\DaemonExample;

class DaemonTest extends TestCase
{
    public function testExecutesOneTimeWithinSpecifiedIntervalInSeconds()
    {
        $intervalInSeconds = 60 * 60;
        $daemon = new DaemonExample($intervalInSeconds);

        $this->assertEquals(0, $daemon->executeCount);
        
        $daemon->exec();
        $daemon->exec();
        $this->assertEquals(1, $daemon->executeCount);

        // Не проверяем со sleep() и запуском после него, потому что в зависимости
        // от ПК время выполнения скрипта может быть разным и трудно подобрать
        // правильный интервал времени.
    }
}