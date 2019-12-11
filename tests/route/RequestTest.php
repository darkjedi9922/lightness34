<?php

use PHPUnit\Framework\TestCase;
use frame\route\Request;

class RequestTest extends TestCase
{
    public function testDeterminesAjaxRequestWithTheSpecialAjaxHeader()
    {
        unset($_SERVER['HTTP_X_REQUESTED_WITH']);
        $this->assertFalse(Request::isAjax());
        
        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
        $this->assertTrue(Request::isAjax());
    }
}