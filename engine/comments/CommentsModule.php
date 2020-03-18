<?php namespace engine\comments;

use frame\modules\Module;
use frame\auth\RightsDesc;

class CommentsModule extends Module
{
    public function createRightsDescription(): ?RightsDesc
    {
        return new CommentsRightsDesc;
    }
}