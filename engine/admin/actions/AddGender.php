<?php namespace engine\admin\actions;

use frame\actions\ActionBody;
use frame\auth\InitAccess;
use engine\users\Group;
use engine\users\Gender;
use frame\actions\fields\StringField;

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
            'name' => StringField::class
        ];
    }

    public function initialize(array $get)
    {
        InitAccess::accessRight('users', 'configure-genders');
    }

    public function validate(array $post, array $files): array
    {
        $errors = [];
        /** @var StringField $name */ $name = $post['name'];
        if ($name->isEmpty()) $errors[] = static::E_NO_NAME;
        return $errors;
    }

    public function succeed(array $post, array $files)
    {
        $gender = new Gender;
        $gender->name = $post['name']->get();
        $gender->insert();
    }
}