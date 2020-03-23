<?php

use PHPUnit\Framework\TestCase;
use frame\stdlib\drivers\route\UrlRequest;
use frame\core\Core;

class UrlRequestTest extends TestCase
{
    public function testDeterminesAjaxRequestWithTheSpecialAjaxHeader()
    {
        $app = new Core;

        unset($_SERVER['HTTP_X_REQUESTED_WITH']);
        $this->assertFalse(UrlRequest::getDriver()->isAjax());
        
        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
        $this->assertTrue(UrlRequest::getDriver()->isAjax());
    }
}