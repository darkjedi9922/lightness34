<?php namespace engine\comments;

use frame\modules\RightsDesc;
use engine\users\User;

class CommentsRightsDesc extends RightsDesc
{
    public function listRights(): array
    {
        return [
            'add' => 'Adding a new comment',
            'edit-all' => 'Editing all comments',
            'edit-own' => 'Editing own comments',
            'delete-all' => 'Deleting all comments',
            'delete-own' => 'Deleting own comments'
        ];
    }

    /** @param Comment $object */
    public function additionCheck(string $right, User $user, $object = null): bool
    {
        switch ($right) {
            case 'edit-own':
            case 'delete-own':
                return $user->id === $object->author_id; 
        }
        return true;
    }
}