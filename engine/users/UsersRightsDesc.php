<?php namespace engine\users;

use frame\modules\RightsDesc;
use frame\modules\GroupUser;
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

    public function listAdditionChecks(GroupUser $user): array
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