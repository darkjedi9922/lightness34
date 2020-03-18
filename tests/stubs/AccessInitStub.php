<?php namespace tests\stubs;

use frame\core\Core;
use frame\tools\init\AccessInit;
use frame\modules\Module;
use frame\auth\UserRights;
use frame\auth\GroupUser;
use frame\auth\RightsStore;
use tests\modules\drivers\RightsStoreStub;

class AccessInitStub extends AccessInit
{
    protected function createUserRights(Module $module, GroupUser $for): UserRights
    {
        $app = new Core;
        $app->replaceDriver(RightsStore::class, RightsStoreStub::class);
        $desc = $module->createRightsDescription();
        if (!$desc) throw new \Exception('The module has no rights desc');
        return new UserRights($desc, $module->getId(), $for);
    }
}