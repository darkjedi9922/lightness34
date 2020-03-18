<?php namespace tests\stubs;

use frame\modules\Module;
use frame\auth\RightsDesc;

class ModuleStub extends Module
{
    public function createRightsDescription(): ?RightsDesc
    {
        return new RightsDescStub;
    }
}