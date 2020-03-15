<?php
use PHPUnit\Framework\TestCase;
use frame\modules\Modules;
use tests\stubs\ModuleStub;

class ModulesTest extends TestCase
{
    public function testSetsModuleAndFindsItByName()
    {
        $modules = new Modules;
        $modules->set(new ModuleStub('test'));
        $this->assertInstanceOf(ModuleStub::class, $modules->findByName('test'));
    }

    public function testSetsModuleAndFindsItById()
    {
        $modules = new Modules;
        $testModule = new ModuleStub('test');
        $moduleId = $testModule->getId();
        $modules->set($testModule);
        $this->assertInstanceOf(ModuleStub::class, $modules->findById($moduleId));
    }
}