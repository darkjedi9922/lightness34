<?php
use PHPUnit\Framework\TestCase;
use frame\tools\Semaphores;

class SemaphoresTest extends TestCase
{
    public function testThereIsNoErrorWhenSyncronizeReturnsNothing()
    {
        Semaphores::synchronize('test', true, function () {});
        $this->assertTrue(true);
    }
}