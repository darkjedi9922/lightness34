<?php namespace engine\articles;

use frame\modules\RightsDesc;
use engine\articles\Article;
use engine\users\User;

class ArticlesRightsDesc extends RightsDesc
{
    public function listRights(): array
    {
        return [
            'add' => 'Adding a new article',
            'edit-all' => 'Edit all articles',
            'edit-own' => 'Edit own article',
            'delete-all' => 'Delete all articles',
            'delete-own' => 'Delete own article'
        ];
    }

    public function listAdditionChecks(User $user): array
    {
        $ownCheck = function (Article $object) use ($user) {
            return $user->id === $object->author_id;
        };

        return [
            'edit-own' => $ownCheck,
            'delete-own' => $ownCheck
        ];
    }
}