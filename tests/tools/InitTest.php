<?php

use PHPUnit\Framework\TestCase;
use engine\users\User;
use frame\errors\HttpError;
use frame\tools\init\AccessInit;
use tests\stubs\ModuleStub;
use tests\stubs\AccessInitStub;

class InitTest extends TestCase
{
    public function testGroupAccessThrowsForbiddenIfAUserDoesNotReferToSomeGroup()
    {
        $user = new User(['id' => 1, 'group_id' => 2]);

        $this->expectException(HttpError::class);
        $this->expectExceptionCode(HttpError::FORBIDDEN);
        
        $init = new AccessInit($user);
        $init->accessGroup(1);
    }

    public function testRightAccessThrowsForbiddenIfAUserDoesNotHaveSomeRight()
    {
        $module = new ModuleStub('stub');
        $user = new User(['id' => 1, 'group_id' => 1]);

        $init = new AccessInitStub($user);

        $init->accessRight($module, 'make');
        $init->accessRight($module, 'create');

        $this->expectException(HttpError::class);
        $this->expectExceptionCode(HttpError::FORBIDDEN);

        $init->accessRight($module, 'add');
    }
}