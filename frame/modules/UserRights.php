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
        $this->rights = $this->createGroupRights($desc, $moduleId, $user);
    }

    public function can(string $right, $object = null): bool
    {
        return $this->rights->can($right) && 
            $this->desc->additionCheck($right, $this->user, $object);
    }

    /**
     * @param array $rights is an array ['right' => $object, 'right2' => null]
     * The object like in self::can(). If there is no need in an object, set null.
     */
    public function canOneOf(array $rights): bool
    {
        foreach ($rights as $right => $object)
            if ($this->can($right, $object)) return true;
        return false;
    }

    protected function createGroupRights(
        RightsDesc $desc, 
        int $moduleId,
        User $user
    ): GroupRights {
        return new GroupRights($desc, $moduleId, $user->group_id);
    }
}