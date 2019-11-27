<?php namespace frame\modules;

use engine\users\User;

abstract class RightsDesc
{
    /**
     * Ассоциативный массив ['right' => 'description']
     */
    public abstract function listRights(): array;

    /**
     * Ассоциативный массив ['right' => ['right1', 'right2']]
     * 
     * Каждое право состоит из вложенных прав. Ими могут быть любые имена прав,
     * определенные в любом из методов (включая этот, только в рекурсию не попадите).
     * 
     * Каждое право в этом массиве будет проходить проверку, если хотя бы одно из
     * вложенных правил будет проходить.
     */
    public function complexRights(): array { return []; }

    public function additionCheck(string $right, User $user, $object = null): 
        bool { return true; }

    public function isListed(string $right): bool
    {
        return isset($this->listRights()[$right]);
    }

    public function isComplex(string $right): bool
    {
        return isset($this->complexRights()[$right]);
    }
}