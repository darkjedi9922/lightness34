<?php

use PHPUnit\Framework\TestCase;
use frame\tools\Client;

class ClientTest extends TestCase
{
    /**
     * @runInSeparateProcess
     */
    public function testUniqueClientId()
    {
        $cid = Client::getId();
        $this->assertEmpty(!$cid);
    }
}