<?php namespace tests\stubs;

use frame\modules\UserRights;
use frame\modules\RightsDesc;
use frame\modules\GroupRights;
use engine\users\User;

class UserRightsStub extends UserRights
{
    protected function createGroupRights(
        RightsDesc $desc, 
        int $moduleId, 
        User $user
    ): GroupRights {
        return new GroupRightsStub($desc, $moduleId, $user->group_id);
    }
}