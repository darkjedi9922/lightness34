<?php namespace engine\users;

use frame\modules\RightsDesc;
use frame\modules\GroupUser;
use engine\users\Group;

class UsersRightsDesc extends RightsDesc
{
    public function listRights(): array
    {
        return [
            'add' => 'Add new users',
            'see-list' => 'See a list of users',
            'see-own' => 'See own profile',
            'see-others' => 'See the profile of others',
            'edit-all' => 'Edit all user profiles',
            'edit-own' => 'Edit own user profile',
            'setup' => 'Setup the module settings',
            'configure-genders' => 'Add, edit and delete genders'
        ];
    }

    public function listAdditionChecks(GroupUser $user): array
    {
        $ownCheck = function (User $object) use ($user) {
            return $user->id === $object->id;
        };
        return [
            'see-own' => $ownCheck,
            'see-others' => function (User $object) use ($user) {
                return $user->id !== $object->id;
            },
            'edit-own' => $ownCheck,
            'edit-all' => function (User $object) use ($user) {
                return $object->group_id !== Group::ROOT_ID
                    || $user->group_id === Group::ROOT_ID;
            }
        ];
    }
}