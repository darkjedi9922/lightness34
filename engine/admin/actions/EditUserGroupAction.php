<?php namespace engine\admin\actions;

use frame\actions\ActionBody;
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
class EditUserGroupAction extends ActionBody
{
    /** @var Group */
    private $group;

    public function listGet(): array
    {
        return [
            'id' => self::GET_INT
        ];
    }

    public function listPost(): array
    {
        return [
            'name' => self::POST_TEXT,
            'icon' => self::POST_TEXT
        ];
    }

    public function initialize(array $get)
    {
        $this->group = Group::selectIdentity($get['id']);
        Init::require($this->group !== null);
        Init::accessGroup(Group::ROOT_ID);
    }

    public function succeed(array $post, array $files)
    {
        $this->group->name = $post['name'];
        $this->group->icon = $post['icon'];
        $this->group->update();
    }
}
