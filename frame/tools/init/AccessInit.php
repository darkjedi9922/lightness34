<?php namespace frame\tools\init;

use frame\errors\HttpError;
use engine\users\User;

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
}