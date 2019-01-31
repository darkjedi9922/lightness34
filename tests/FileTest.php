<?php

use PHPUnit\Framework\TestCase;
use frame\tools\File;

class FileTest extends TestCase
{
    public function testReturnsMime()
    {
        $file = new File(ROOT_DIR . '/tests/config/some.json');
        $this->assertEquals('text/plain', $file->getMime());
    }
}