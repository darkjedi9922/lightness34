<?php namespace engine\articles;

use frame\modules\Module as FrameModule;
use frame\modules\RightsDesc as FrameRightsDesc;

class Module extends FrameModule
{
    public function createRightsDescription(): ?FrameRightsDesc
    {
        return new RightsDesc;
    }
}