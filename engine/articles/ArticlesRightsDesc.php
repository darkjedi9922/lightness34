<?php namespace engine\articles;

use frame\modules\RightsDesc;
use frame\modules\GroupUser;
use engine\articles\Article;

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

    public function listAdditionChecks(GroupUser $user): array
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