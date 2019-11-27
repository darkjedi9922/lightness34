<?php namespace engine\users;

use frame\modules\RightsDesc;
use engine\users\User;

class UsersRightsDesc extends RightsDesc
{
    public function listRights(): array
    {
        return [
            'edit-all' => 'Edit all user profiles',
            'edit-own' => 'Edit own user profile'
        ];
    }

    public function complexRights(): array
    {
        return [
            'edit' => ['edit-all', 'edit-own'],
            'delete-avatar' => ['edit']
        ];
    }

    public function additionCheck(string $right, User $user, $object = null): bool
    {
        switch ($right) {
            case 'edit-own': return $user->id === $object;
            default: return true;
        }
    }
}