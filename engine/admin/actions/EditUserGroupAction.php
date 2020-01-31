<?php namespace engine\admin\actions;

use frame\actions\ActionBody;
use frame\tools\Init;
use engine\users\Group;
use frame\actions\fields\IntegerField;
use frame\actions\fields\StringField;

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
            'id' => IntegerField::class
        ];
    }

    public function listPost(): array
    {
        return [
            'name' => StringField::class,
            'icon' => StringField::class
        ];
    }

    public function initialize(array $get)
    {
        $this->group = Group::selectIdentity($get['id']->get());
        Init::require($this->group !== null);
        Init::accessGroup(Group::ROOT_ID);
    }

    public function succeed(array $post, array $files)
    {
        $this->group->name = $post['name']->get();
        $this->group->icon = $post['icon']->get();
        $this->group->update();
    }
}
