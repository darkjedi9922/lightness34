<?php namespace engine\comments;

use frame\modules\Module as FrameModule;
use frame\modules\RightsDesc as FrameRightsDesc;

class Module extends FrameModule
{
    public function createRightsDescription(): ?FrameRightsDesc
    {
        return null;   
    }
}