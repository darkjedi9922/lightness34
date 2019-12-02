<?php namespace engine\admin\actions;

use engine\users\cash\user_me;
use frame\actions\Action;
use engine\users\Group;
use frame\database\Records;
use frame\tools\Init;

/**
 * Параметры:
 * id: id группы пользователя.
 * Она должна существовать.
 * Не быть системной.
 * Права: root.
 */
class DeleteUserGroupAction extends Action
{
    /** @var Group */
    private $group;

    protected function initialize(array $get)
    {
        $id = $get['id'] ?? null;
        Init::require($id !== null);
        $this->group = Group::selectIdentity($id);
        Init::require($this->group !== null);
        Init::require(!$this->group->isSystem());
        Init::access((int) user_me::get()->group_id === Group::ROOT_ID);
    }

    protected function succeed(array $post, array $files)
    {
        Records::select('users', ['group_id' => $this->group->id])->update([
            'group_id' => Group::USER_ID
        ]);
        $this->group->delete();
    }
}