<?php namespace engine\admin\actions;

use frame\actions\Action;
use frame\tools\Init;
use engine\users\Group;

/**
 * Параметры:
 * id: id группы пользователя.
 * Она должна существовать.
 * Права: root.
 * Данные:
 * name (не обязательно): название
 * icon (не обязательно): путь к файлу иконке
 */
class EditUserGroupAction extends Action
{
    /** @var Group */
    private $group;

    // protected function listGet(): array
    // {
    //     return [
    //         'id' => [self::GET_INT, 'Id of the group']
    //     ];
    // }

    // protected function listPost(): array
    // {
    //     return [
    //         'name' => [self::POST_TEXT, 'New name of the group'],
    //         'icon' => [self::POST_TEXT, 'Path to new icon of the group']
    //     ];
    // }

    protected function initialize(array $get)
    {
        $id = (int) ($get['id'] ?? -1);
        Init::require($id !== -1);
        $this->group = Group::selectIdentity($id);
        Init::require($this->group !== null);
        Init::accessGroup(Group::ROOT_ID);
    }

    protected function succeed(array $post, array $files)
    {
        if (isset($post['name'])) $this->group->name = $post['name'];
        if (isset($post['icon'])) $this->group->icon = $post['icon'];
        $this->group->update();
    }

    protected function getDataToSave(): array
    {
        return ['name', 'icon'];
    }
}
