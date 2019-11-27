<?php namespace engine\articles;

use frame\modules\Module;
use frame\modules\RightsDesc;

class ArticlesModule extends Module
{
    public function createRightsDescription(): ?RightsDesc
    {
        return new ArticlesRightsDesc;
    }
}