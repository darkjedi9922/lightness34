<?php namespace frame\tools\init;

use frame\errors\HttpError;
use engine\users\User;
use frame\modules\UserRights;
use frame\modules\Module;

class AccessInit
{
    private $for;

    public function __construct(User $for)
    {
        $this->for = $for;
    }

    /** @throws HttpError FORBIDDEN */
    public function access(bool $expr)
    {
        if ($expr === false) throw new HttpError(HttpError::FORBIDDEN);
    }

    /** @throws HttpError FORBIDDEN */
    public function accessGroup(int $groupId)
    {
        $this->access((int) $this->for->group_id === $groupId);
    }

    /** 
     * @throws HttpError FORBIDDEN
     * @throws \Exception if there is no such module rights.
     */
    public function accessRight(Module $module, string $right, $object = null)
    {
        $rights = $this->createUserRights($module, $this->for);
        if (!$rights->can($right, $object))
            throw new HttpError(HttpError::FORBIDDEN);
    }

    /**
     * @throws \Exception if there is no such module rights.
     */
    protected function createUserRights(Module $module, User $for): UserRights
    {
        $desc = $module->createRightsDescription();
        if ($desc === null) throw new \Exception('The module has no rights desc');
        return new UserRights($desc, $module->getId(), $for);
    }
}