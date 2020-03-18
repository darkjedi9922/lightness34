<?php namespace engine\messages;

use frame\modules\Module;
use frame\auth\RightsDesc;

class MessagesModule extends Module
{
    public function createRightsDescription(): ?RightsDesc
    {
        return new MessagesRightsDesc;
    }
}