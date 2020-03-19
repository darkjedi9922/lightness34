<?php

use PHPUnit\Framework\TestCase;
use drivers\route\UrlRequest;
use frame\core\Core;

class UrlRequestTest extends TestCase
{
    public function testDeterminesAjaxRequestWithTheSpecialAjaxHeader()
    {
        $app = new Core;

        unset($_SERVER['HTTP_X_REQUESTED_WITH']);
        $this->assertFalse(UrlRequest::get()->isAjax());
        
        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
        $this->assertTrue(UrlRequest::get()->isAjax());
    }
}