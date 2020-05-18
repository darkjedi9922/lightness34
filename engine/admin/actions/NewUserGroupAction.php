<?php namespace engine\admin\actions;

use engine\users\cash\user_me;
use engine\users\Group;
use frame\actions\ActionBody;
use frame\actions\fields\StringField;
use frame\database\Records;
use frame\auth\InitAccess;

class NewUserGroupAction extends ActionBody
{
    const E_NO_NAME = 1;

    public function listPost(): array
    {
        return [
            'name' => StringField::class
        ];
    }

    public function initialize(array $get)
    {
        InitAccess::access((int)user_me::get()->group_id === Group::ROOT_ID);
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
        Records::from('user_groups')->insert([
            'name' => $post['name']->get()
        ]);
    }
}