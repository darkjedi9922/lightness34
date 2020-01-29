<?php namespace engine\admin\actions;

use frame\actions\ActionBody;
use frame\tools\Init;
use engine\users\Group;
use engine\users\Gender;

/**
 * Права: root.
 * Данные:
 * name: имя пола.
 */
class AddGender extends ActionBody
{
    const E_NO_NAME = 1;

    public function listPost(): array
    {
        return [
            'name' => self::POST_TEXT
        ];
    }

    public function initialize(array $get)
    {
        Init::accessGroup(Group::ROOT_ID);
    }

    public function validate(array $post, array $files): array
    {
        $errors = [];
        if (!$post['name']) $errors[] = static::E_NO_NAME;
        return $errors;
    }

    public function succeed(array $post, array $files)
    {
        $gender = new Gender;
        $gender->name = $post['name'];
        $gender->insert();
    }
}