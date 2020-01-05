<?php

use PHPUnit\Framework\TestCase;
use tests\stubs\RightsDescStub;
use tests\stubs\UserRightsStub;
use tests\stubs\ModuleStub;
use engine\users\User;

class UserRightsTest extends TestCase
{
    public function testIfARightHasAnAdditionCheckForUserItIsCheckedToo()
    {
        $desc = new RightsDescStub;
        $module = new ModuleStub('stub');
        $user = new User(['id' => 1, 'group_id' => 2]); // has right 'see-own'
        $rights = new UserRightsStub($desc, $module->getId(), $user);

        $this->assertTrue($rights->can('see-own', $user->id));
        $this->assertFalse($rights->can('see-own', $user->id + 1));
    }

    public function testARightCheckCanHaveVariableNumberOfDifferentArgs()
    {
        $desc = new RightsDescStub;
        $module = new ModuleStub('stub');
        $user = new User(['id' => 1, 'group_id' => 3]); // has right 'execute-order'
        $rights = new UserRightsStub($desc, $module->getId(), $user);

        // There are a check which return true only if both args equal 6.
        $this->assertTrue($rights->can('execute-order', 6, 6));
        $this->assertFalse($rights->can('execute-order', 4, 2));
    }
}