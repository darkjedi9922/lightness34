<?php namespace engine\admin\actions;

use engine\users\cash\user_me;
use frame\actions\ActionBody;
use engine\users\Group;
use frame\actions\fields\IntegerField;
use frame\database\Records;
use frame\tools\Init;

/**
 * Параметры:
 * id: id группы пользователя.
 * Она должна существовать.
 * Не быть системной.
 * Права: root.
 */
class DeleteUserGroupAction extends ActionBody
{
    /** @var Group */
    private $group;

    public function listGet(): array
    {
        return [
            'id' => IntegerField::class
        ];
    }

    public function initialize(array $get)
    {
        Init::accessGroup(Group::ROOT_ID);
        $this->group = Group::selectIdentity($get['id']->get());
        Init::require($this->group !== null);
        Init::require(!$this->group->isSystem());
    }

    public function succeed(array $post, array $files)
    {
        Records::from('users', ['group_id' => $this->group->id])->update([
            'group_id' => Group::USER_ID
        ]);
        $this->group->delete();
    }
}