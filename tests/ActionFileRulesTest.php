<?php

use PHPUnit\Framework\TestCase;
use frame\actions\UploadedFile;
use frame\actions\RuleResult;
use frame\rules\Rules;
use frame\tools\File;

class ActionFileRulesTest extends TestCase
{
    public function testMustLoadRuleReturnsSuccessIfRuleIsTrueAndFileIsLoaded()
    {
        $mustLoad = Rules::loadRule('file/must-load');
        $file = new UploadedFile([
            'name' => 'my-new-avatar.jpg',
            'type' => 'image/gif',
            'size' => 1024 * 1024, // 1 MB
            'tmp_name' => '',
            'error' => UploadedFile::UPLOAD_ERR_OK
        ]);

        $this->assertTrue($mustLoad(true, $file, new RuleResult)->isSuccess());
    }

    public function testMustLoadRuleReturnsSuccessIfRuleIsFalseAndFileIsLoaded()
    {
        $mustLoad = Rules::loadRule('file/must-load');
        $file = new UploadedFile([
            'name' => 'my-new-avatar.jpg',
            'type' => 'image/gif',
            'size' => 1024 * 1024, // 1 MB
            'tmp_name' => '',
            'error' => UploadedFile::UPLOAD_ERR_OK
        ]);

        $this->assertTrue($mustLoad(false, $file, new RuleResult)->isSuccess());
    }

    public function testMustLoadRuleReturnsSuccessIfRuleIsFalseAndFileIsNotLoaded()
    {
        $mustLoad = Rules::loadRule('file/must-load');
        $file = new UploadedFile([
            'name' => '',
            'type' => '',
            'size' => 0,
            'tmp_name' => '',
            'error' => UploadedFile::UPLOAD_ERR_NO_FILE
        ]);

        $this->assertTrue($mustLoad(false, $file, new RuleResult)->isSuccess());
    }

    public function testMustLoadRuleReturnsFailIfRuleIsTrueAndFileIsNotLoaded()
    {
        $mustLoad = Rules::loadRule('file/must-load');
        $file = new UploadedFile([
            'name' => '',
            'type' => '',
            'size' => 0,
            'tmp_name' => '',
            'error' => UploadedFile::UPLOAD_ERR_NO_FILE
        ]);

        $this->assertTrue($mustLoad(true, $file, new RuleResult)->isFail());
    }

    public function testMaxSizeRuleReturnsSuccessIfFileSizeIsMoreThanThatValue()
    {
        $maxSize = Rules::loadRule('file/max-size');
        $file = new UploadedFile([
            'name' => 'my-new-avatar.jpg',
            'type' => 'image/gif',
            'size' => 1024 * 1024, // 1 MB
            'tmp_name' => '',
            'error' => UploadedFile::UPLOAD_ERR_OK
        ]);

        $this->assertTrue($maxSize([2, 'MB'], $file, new RuleResult)->isSuccess());
    }

    public function testMaxSizeRuleReturnsFailIfFileSizeIsMoreThanThatValue()
    {
        $maxSize = Rules::loadRule('file/max-size');
        $file = new UploadedFile([
            'name' => 'my-new-avatar.jpg',
            'type' => 'image/gif',
            'size' => 1024 * 1024, // 1 MB
            'tmp_name' => '',
            'error' => UploadedFile::UPLOAD_ERR_OK
        ]);

        $this->assertTrue($maxSize([750, 'KB'], $file, new RuleResult)->isFail());
    }

    public function testMaxSizeRuleReturnsFailIfThereIsUploadIniSizeError()
    {
        $maxSize = Rules::loadRule('file/max-size');
        $file = new UploadedFile([
            'name' => 'my-new-avatar.jpg',
            'type' => 'image/gif',
            'size' => 1024 * 1024, // 1 MB
            'tmp_name' => '',
            'error' => UploadedFile::UPLOAD_ERR_INI_SIZE
        ]);

        $this->assertTrue($maxSize([1, 'GB'], $file, new RuleResult)->isFail());
    }

    public function testMaxSizeRuleReturnsFailIfThereIsUploadHtmlFormSizeError()
    {
        $maxSize = Rules::loadRule('file/max-size');
        $file = new UploadedFile([
            'name' => 'my-new-avatar.jpg',
            'type' => 'image/gif',
            'size' => 1024 * 1024, // 1 MB
            'tmp_name' => '',
            'error' => UploadedFile::UPLOAD_ERR_FORM_SIZE
        ]);

        $this->assertTrue($maxSize([1, 'GB'], $file, new RuleResult)->isFail());
    }

    public function testTypeRuleReturnsSuccessIfTheFileHasThatType()
    {
        $mime = Rules::loadRule('file/mime');
        $file = new File(ROOT_DIR . '/tests/config/some.json');

        $this->assertTrue($mime(['text/plain'], $file, new RuleResult)->isSuccess());
    }
}