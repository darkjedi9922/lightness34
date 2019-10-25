<?php namespace frame\modules;

use engine\users\User;

interface RightsDesc
{
    /**
     * Ассоциативный массив ['right' => 'description']
     */
    public function listRights(): array;

    public function additionCheck(string $right, User $user, $object = null): bool;
}