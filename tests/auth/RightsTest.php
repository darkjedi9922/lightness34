<?php

use PHPUnit\Framework\TestCase;
use frame\auth\Rights;
use frame\auth\Auth;

class RightsTest extends TestCase
{
    /** @runInSeparateProcess */
    public function testCanLoginIfLogout()
    {
        $auth = new Auth;
        $auth->logout();

        $rights = new Rights;
        $canLogin = $rights->can('login');
        
        $this->assertTrue($canLogin);
    }

    /** @runInSeparateProcess */
    public function testCanNotLoginIfLogout()
    {
        $auth = new Auth;
        $auth->login();

        $rights = new Rights;
        $canLogin = $rights->can('login');
        
        $this->assertFalse($canLogin);
    }

    public function testCanMethodThrowsErrorIfThereIsNoDefinedRightThatPassesTo()
    {
        $rights = new Rights;
        $this->expectException(Exception::class);
        $rights->can('non-existence-right');
    }
}