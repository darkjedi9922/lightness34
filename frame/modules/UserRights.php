<?php namespace frame\modules;

use engine\users\User;
use frame\modules\RightsDesc;

class UserRights
{
    private $desc;
    private $user;
    private $rights;

    public function __construct(RightsDesc $desc, int $moduleId, User $user)
    {
        $this->user = $user;

        $this->desc = $desc;
        $this->rights = new GroupRights($desc, $moduleId, $user->group_id);
    }

    public function can(string $right, $object = null): bool
    {
        return $this->rights->can($right) && 
            $this->desc->additionCheck($right, $this->user, $object);
    }
}