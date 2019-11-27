<?php namespace frame\modules;

use engine\users\User;
use engine\users\Group;
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
        if ($this->user->group_id === Group::ROOT_ID) return true;

        if ($this->desc->isComplex($right)) {
            foreach ($this->desc->complexRights()[$right] as $innerRight) {
                if ($this->can($innerRight, $object)) return true;
            }
            return false;
        }

        return $this->rights->can($right) && 
            $this->desc->additionCheck($right, $this->user, $object);
    }
}