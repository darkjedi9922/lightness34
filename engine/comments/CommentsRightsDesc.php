<?php namespace engine\comments;

use frame\auth\RightsDesc;
use frame\auth\GroupUser;

class CommentsRightsDesc extends RightsDesc
{
    public function listRights(): array
    {
        return [
            'add' => 'Adding a new comment',
            'edit-all' => 'Editing all comments',
            'edit-own' => 'Editing own comments',
            'delete-all' => 'Deleting all comments',
            'delete-own' => 'Deleting own comments',
            'configure' => 'Configure the module',
        ];
    }

    public function listAdditionChecks(GroupUser $user): array
    {
        $ownCheck = function (Comment $object) use ($user) {
            return $user->id === $object->author_id;
        };

        return [
            'edit-own' => $ownCheck,
            'delete-own' => $ownCheck
        ];
    }
}