<?php namespace engine\admin\actions;

use engine\users\cash\user_me;
use engine\users\Group;
use frame\actions\ActionBody;
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

    public function initialize(array $get)
    {
        Init::access((int)user_me::get()->group_id === Group::ROOT_ID);
    }
    
    public function validate(array $post, array $files): array
    {
        $errors = [];

        if (!isset($post['name']) || $post['name'] === '') 
            $errors[] = static::E_NO_NAME;
        
        return $errors;
    }

    public function succeed(array $post, array $files)
    {
        Records::select('user_groups', [
            'name' => $post['name'],
            'icon' => $post['icon'] ?? ''
        ])->insert();
    }

    public function getPostToSave(): array
    {
        return ['name', 'icon'];
    }
}