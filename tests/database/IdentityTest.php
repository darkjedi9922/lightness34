<?php

use PHPUnit\Framework\TestCase;
use tests\stubs\IdentityStub;

class IdentityTest extends TestCase
{
    public function testIdentityCanNotBeCreatedWithDataWithoutId()
    {
        $this->expectException(\Exception::class);
        $identity = new IdentityStub(['name' => 'Stub']);
    }
}