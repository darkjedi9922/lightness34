<?php namespace tests\stubs;

use frame\tools\init\AccessInit;
use frame\modules\Module;
use frame\modules\UserRights;
use engine\users\User;

class AccessInitStub extends AccessInit
{
    protected function createUserRights(Module $module, User $for): UserRights
    {
        $desc = $module->createRightsDescription();
        if (!$desc) throw new \Exception('The module has no rights desc');
        return new UserRightsStub($desc, $module->getId(), $for);
    }
}