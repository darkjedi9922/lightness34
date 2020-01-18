<?php namespace tests\stubs;

use frame\modules\UserRights;
use frame\modules\RightsDesc;
use frame\modules\GroupRights;
use frame\modules\GroupUser;

class UserRightsStub extends UserRights
{
    protected function createGroupRights(
        RightsDesc $desc, 
        int $moduleId, 
        GroupUser $user
    ): GroupRights {
        return new GroupRightsStub($desc, $moduleId, $user->getGroupId());
    }
}