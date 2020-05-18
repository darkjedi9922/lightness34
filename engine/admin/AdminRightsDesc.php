<?php namespace engine\admin;

use frame\auth\RightsDesc;

class AdminRightsDesc extends RightsDesc
{
    public function listRights(): array
    {
        return [
            'enter' => 'Enter into admin panel',
            'see-logs' => 'See logs'
        ];
    }
}