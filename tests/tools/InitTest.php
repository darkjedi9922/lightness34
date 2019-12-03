<?php

use PHPUnit\Framework\TestCase;
use engine\users\User;
use frame\errors\HttpError;
use frame\tools\init\AccessInit;

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
}