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

    public function listAdditionChecks(User $user): array
    {
        return [
            'edit-own' => function (User $object) use ($user) {
                return $user->id === $object->id;
            },
            'edit-all' => function (User $object) use ($user) {
                return $object->group_id !== Group::ROOT_ID
                    || $user->group_id === Group::ROOT_ID;
            }
        ];
    }
}