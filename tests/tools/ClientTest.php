<?php

use PHPUnit\Framework\TestCase;
use frame\http\Client;

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