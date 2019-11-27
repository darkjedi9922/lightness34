<?php namespace engine\admin;

use frame\modules\Module;
use frame\modules\RightsDesc;

class AdminModule extends Module
{
    public function createRightsDescription(): ?RightsDesc
    {
        return new AdminRightsDesc;
    }
}