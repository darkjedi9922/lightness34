<?php namespace engine\admin;

use frame\modules\RightsDesc as FrameRightsDesc;
use engine\users\User;

class RightsDesc implements FrameRightsDesc
{
    public function listRights(): array
    {
        return [
            'enter' => 'Enter into admin panel'
        ];
    }

    public function additionCheck(string $right, User $user, $object = null): bool
    {
        return true;
    }
}