<?php namespace engine\articles;

use frame\modules\RightsDesc;
use engine\users\User;

class ArticlesRightsDesc extends RightsDesc
{
    public function listRights(): array
    {
        return [
            'add' => 'Adding a new article',
            'edit-all' => 'Edit all articles',
            'edit-own' => 'Edit own article'
        ];
    }

    public function complexRights(): array
    {
        return [
            'edit' => ['edit-all', 'edit-own']
        ];
    }

    public function additionCheck(string $right, User $user, $object = null): bool
    {
        switch ($right) {
        case 'edit-own':
            return $user->id === $object->author_id;
        }
        return true;
    }
}