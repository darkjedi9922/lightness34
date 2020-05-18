<?php namespace engine\admin\actions;

use frame\actions\ActionBody;
use engine\users\Group;
use frame\actions\fields\IntegerField;
use frame\database\Records;
use frame\auth\InitAccess;
use frame\route\InitRoute;

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
        InitAccess::accessGroup(Group::ROOT_ID);
        $this->group = Group::selectIdentity($get['id']->get());
        InitRoute::require($this->group !== null);
        InitRoute::require(!$this->group->isSystem());
    }

    public function succeed(array $post, array $files)
    {
        Records::from('users', ['group_id' => $this->group->id])->update([
            'group_id' => Group::USER_ID
        ]);
        $this->group->delete();
    }
}