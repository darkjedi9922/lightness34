<?php

use PHPUnit\Framework\TestCase;

/**
 * @testdox lightness.lib::generate_unique_filename()
 */
final class GenerateUniqueFilenameTest extends TestCase
{
    /**
     * @test
     * @testdox generates unique filename if the file exists yet
     */
    public function test_1()
    {
        $input = 'files/file_1';
        $expected = 'files/file_1_1';

        $actual = generate_unique_filename($input);
        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     * @testdox does not generate unique filename if the file does not exist
     */
    public function test_2()
    {
        $input = 'files/non-existence-file';
        $expected = $input;

        $actual = generate_unique_filename($input);
        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     * @testdox does not generate unique filename if the filename is ''
     */
    public function test_3()
    {
        $input = '';
        $expected = '';

        $actual = generate_unique_filename($input);
        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     * @testdox generates unique filename if the file is dir
     */
    public function test_4()
    {
        $this->assertEquals('files_1', generate_unique_filename(('files')));
    }

    /**
     * @test
     * @testdox generates unique filename if the filename is .
     */
    public function test_5()
    {
        $this->assertEquals('._1', generate_unique_filename('.'));
    }

    /**
     * @test
     * @testdox generates unique filename if the filename is ..
     */
    public function test_6()
    {
        $this->assertEquals('.._1', generate_unique_filename('..'));
    }

    /**
     * @test 
     * @testdox working correct with root path /
     */
    public function test_7()
    {
        $this->assertEquals('/', generate_unique_filename('/'));
        $this->assertEquals('/usr/bin_1', generate_unique_filename('/usr/bin'));
    }

    /**
     * @test
     * @testdox generates unique filename for dirs which path ends with /
     */
    public function test_8()
    {
        $this->assertEquals('files_1', generate_unique_filename('files/'));
    }

    /**
     * @test
     * @testdox generates unique filename 'file_2_2' for 'file_2' if there is 'file_2_1' yet
     */
    public function test_9()
    {
        $this->assertEquals('files/file_2_2', generate_unique_filename('files/file_2'));
    }
}