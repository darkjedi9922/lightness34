<?php namespace engine\messages;

use frame\modules\RightsDesc;

class MessagesRightsDesc extends RightsDesc
{
    public function listRights(): array
    {
        return [
            'use' => 'Use the module for sending messages',
            'setup' => 'Setup the module settings'
        ];
    }
}