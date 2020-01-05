<?php namespace frame\modules;

use engine\users\User;
use frame\modules\RightsDesc;

class UserRights
{
    private $desc;
    private $user;
    private $rights;
    private $additionChecks;

    public function __construct(RightsDesc $desc, int $moduleId, User $user)
    {
        $this->user = $user;

        $this->desc = $desc;
        $this->additionChecks = $desc->listAdditionChecks($user);
        $this->rights = $this->createGroupRights($desc, $moduleId, $user);
    }

    public function can(string $right, ...$additionCheckArgs): bool
    {
        return $this->rights->can($right)
            && (isset($this->additionChecks[$right]) 
                ? $this->additionChecks[$right](...$additionCheckArgs) :
                true
            );
    }

    /**
     * @param array $rights is an array ['right' => [...$args], 'right2' => null]
     * The object like in self::can(). If there is no need in an object, set null.
     */
    public function canOneOf(array $rights): bool
    {
        foreach ($rights as $right => $args)
            if ($this->can($right, ...$args)) return true;
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