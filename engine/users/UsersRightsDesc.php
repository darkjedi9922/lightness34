<?php namespace engine\users;

use frame\modules\RightsDesc;
use engine\users\User;
use engine\users\Group;

class UsersRightsDesc extends RightsDesc
{
    public function listRights(): array
    {
        return [
            'edit-all' => 'Edit all user profiles',
            'edit-own' => 'Edit own user profile'
        ];
    }

    /**
     * @param User $object
     */
    public function additionCheck(string $right, User $user, $object = null): bool
    {
        switch ($right) {
            case 'edit-own': return $user->id === $object->id;
            case 'edit-all': return $object->group_id !== Group::ROOT_ID 
                                 || $user->group_id === Group::ROOT_ID;
            default: return true;
        }
    }
}