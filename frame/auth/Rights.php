<?php namespace frame\auth;

use frame\auth\Auth;

class Rights
{
    public function can(string $right)
    {
        switch ($right) {
            case 'login':
                return !((new Auth)->isLogged());
            case 'logout':
                return (new Auth)->isLogged();
            default:
                throw new \Exception("Right `$right` does not exist.");
        }
    }
}