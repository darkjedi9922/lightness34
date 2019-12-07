<?php namespace tests\stubs;

use frame\modules\RightsDesc;
use engine\users\User;

class RightsDescStub extends RightsDesc
{
    public function listRights(): array
    {
        return [
            'add' => 'Adding something',
            'make' => 'Making something',
            'create' => 'Creating something',
            'see-own' => 'See own id'
        ];
    }

    public function additionCheck(string $right, User $user, $object = null): bool
    {
        switch ($right) {
            case 'see-own': return $user->id === $object;
        }

        return true;
    }
}