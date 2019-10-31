<?php

use PHPUnit\Framework\TestCase;
use frame\auth\Auth;

/** @runTestsInSeparateProcesses */
class AuthTest extends TestCase
{
    private $key = 'some-secret-key';

    public function testLogin()
    {
        $auth = new Auth;
        
        $this->assertFalse($auth->isLogged());
        $auth->login($this->key);
        $this->assertTrue($auth->isLogged());
    }

    public function testSaveLoginState()
    {
        $auth = new Auth;
        $auth->login($this->key);

        $nextPageAuth = new Auth;
        $this->assertTrue($nextPageAuth->isLogged());
    }

    public function testLogout()
    {
        $auth = new Auth;
        $auth->login($this->key);

        $this->assertTrue($auth->isLogged());
        $auth->logout();
        $this->assertFalse($auth->isLogged());
    }

    public function testLoginProcedureTakesAKey()
    {
        $auth = new Auth;
        $auth->login($this->key);

        $key = $auth->getKey();
        $this->assertEquals($this->key, $key);
    }

    public function testRememberLogin()
    {
        $auth = new Auth;
        $auth->login($this->key, true);

        $this->assertTrue($auth->isRemembered());
    }

    public function testNotRememberLogin()
    {
        $auth = new Auth;
        $auth->login($this->key, false);

        $this->assertFalse($auth->isRemembered());
    }
}