<?php namespace engine\admin\actions;

use engine\users\cash\user_me;
use engine\users\Group;
use frame\actions\ActionBody;
use frame\actions\fields\StringField;
use frame\database\Records;
use frame\tools\Init;

/**
 * Права: root.
 * Данные:
 * name: название группы (обязательно)
 * icon: путь к иконке (необязательно)
 */
class NewUserGroupAction extends ActionBody
{
    const E_NO_NAME = 1;

    public function listPost(): array
    {
        return [
            'name' => StringField::class,
            'icon' => StringField::class
        ];
    }

    public function initialize(array $get)
    {
        Init::access((int)user_me::get()->group_id === Group::ROOT_ID);
    }
    
    public function validate(array $post, array $files): array
    {
        $errors = [];

        /** @var StringField $name */ $name = $post['name'];
        if ($name->isEmpty())
            $errors[] = static::E_NO_NAME;
        
        return $errors;
    }

    public function succeed(array $post, array $files)
    {
        Records::from('user_groups', [
            'name' => $post['name']->get(),
            'icon' => $post['icon']->get()
        ])->insert();
    }
}