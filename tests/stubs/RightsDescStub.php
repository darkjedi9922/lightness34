<?php namespace tests\stubs;

use frame\auth\RightsDesc;
use frame\auth\GroupUser;

class RightsDescStub extends RightsDesc
{
    public function listRights(): array
    {
        return [
            'add' => 'Adding something',
            'make' => 'Making something',
            'create' => 'Creating something',
            'see-own' => 'See own id',
            'execute-order' => 'Execute order 66'
        ];
    }

    public function listAdditionChecks(GroupUser $user): array
    {
        return [
            'see-own' => function (int $userId) use ($user) {
                return $user->id === $userId;
            },
            'execute-order' => function (int $firstNumber, int $secondNumber) {
                return $firstNumber === 6 && $secondNumber === 6;
            }
        ];
    }
}