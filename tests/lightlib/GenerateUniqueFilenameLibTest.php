<?php

use PHPUnit\Framework\TestCase;
use function lightlib\generate_unique_filename;

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
        $input = ROOT_DIR.'/tests/files/file_1';
        $expected = ROOT_DIR.'/tests/files/file_1_1';

        $actual = generate_unique_filename($input);
        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     * @testdox does not generate unique filename if the file does not exist
     */
    public function test_2()
    {
        $input = ROOT_DIR.'/tests/files/non-existence-file';
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
        $this->assertEquals(ROOT_DIR.'/tests/files_1', 
            generate_unique_filename(ROOT_DIR.'/tests/files'));
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
     * @testdox generates unique filename for dirs which path ends with /
     */
    public function test_8()
    {
        $this->assertEquals(ROOT_DIR.'/tests/files_1', 
            generate_unique_filename(ROOT_DIR.'/tests/files/'));
    }

    /**
     * @test
     * @testdox generates unique filename 'file_2_2' for 'file_2' if there is 'file_2_1' yet
     */
    public function test_9()
    {
        $this->assertEquals(ROOT_DIR.'/tests/files/file_2_2', 
            generate_unique_filename(ROOT_DIR.'/tests/files/file_2'));
    }
}