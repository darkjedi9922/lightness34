<?php namespace frame\modules;

use engine\users\User;

abstract class RightsDesc
{
    /**
     * Ассоциативный массив ['right' => 'description']
     */
    public abstract function listRights(): array;

    public function additionCheck(string $right, User $user, $object = null): 
        bool { return true; }
}