<?php

use PHPUnit\Framework\TestCase;
use frame\auth\Auth;

/** @runTestsInSeparateProcesses */
class AuthTest extends TestCase
{
    public function testLogin()
    {
        $auth = new Auth;
        
        $this->assertFalse($auth->isLogged());
        $auth->login();
        $this->assertTrue($auth->isLogged());
    }

    public function testSaveLoginState()
    {
        $auth = new Auth;
        $auth->login();

        $nextPageAuth = new Auth;
        $this->assertTrue($nextPageAuth->isLogged());
    }

    public function testLogout()
    {
        $auth = new Auth;
        $auth->login();

        $this->assertTrue($auth->isLogged());
        $auth->logout();
        $this->assertFalse($auth->isLogged());
    }
}