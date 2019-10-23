<?php namespace engine\articles;

use frame\modules\RightsDesc as FrameRightsDesc;
use engine\users\User;

class RightsDesc implements FrameRightsDesc
{
    public function listRights(): array
    {
        return [
            'add' => 'Adding a new article',
            'edit-all' => 'Edit all articles',
            'edit-own' => 'Edit own article'
        ];
    }

    public function additionCheck(
        string $right, 
        User $subject = null, 
        $object = null
    ): bool {
        switch ($right) {
        case 'edit-own':
            return $subject->id === $object->author_id;
        }
        return true;
    }
}