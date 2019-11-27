<?php namespace engine\users;

use frame\modules\Module;
use frame\modules\RightsDesc;

class UsersModule extends Module
{
    public function createRightsDescription(): ?RightsDesc
    {
        return new UsersRightsDesc;
    }
}