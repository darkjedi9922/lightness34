<?php namespace tests\stubs;

use frame\modules\RightsDesc;

class RightsDescStub extends RightsDesc
{
    public function listRights(): array
    {
        return [
            'add' => 'Adding something',
            'make' => 'Making something',
            'create' => 'Creating something'
        ];
    }
}