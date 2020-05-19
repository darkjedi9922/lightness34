<?php namespace engine\users;

use frame\lists\paged\IdentityPagedList;
use engine\users\User;
use frame\config\ConfigRouter;

class UserPagedList extends IdentityPagedList
{
    public function getIdentityClass(): string
    {
        return User::class;
    }

    protected function loadPageLimit(): int
    {
        return ConfigRouter::getDriver()->findConfig('users')->{'list.amount'};
    }

    public function getOrderFields(): array
    {
        return ['id' => 'ASC'];
    }
}