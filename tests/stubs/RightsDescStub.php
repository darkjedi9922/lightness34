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

    public function listAdditionChecks(User $user): array
    {
        return [
            'see-own' => function (int $userId) use ($user) {
                return $user->id === $userId;
            }
        ];
    }
}