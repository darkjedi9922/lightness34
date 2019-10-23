<?php namespace frame\modules;

use engine\users\User;

interface RightsDesc
{
    public function listRights(): array;
    public function additionCheck(string $right, 
        User $user = null, $object = null): bool;
}