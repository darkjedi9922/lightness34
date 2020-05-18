<?php namespace engine\admin\actions;

use frame\actions\ActionBody;
use engine\users\Gender;
use frame\auth\InitAccess;
use frame\route\InitRoute;
use frame\actions\fields\IntegerField;
use frame\actions\fields\StringField;

/**
 * Параметры:
 * id: id пола. Должен существовать.
 * Права: root.
 * Данные:
 * name (не обязательно): новое название пола
 */
class EditGender extends ActionBody
{
    const E_NO_NAME = 1;

    /** @var Gender */
    private $gender;

    public function listGet(): array
    {
        return [
            'id' => IntegerField::class
        ];
    }

    public function listPost(): array
    {
        return [
            'name' => StringField::class
        ];
    }

    public function initialize(array $get)
    {
        InitAccess::accessRight('users', 'configure-genders');
        $this->gender = Gender::selectIdentity($get['id']->get());
        InitRoute::require($this->gender !== null);
    }

    public function validate(array $post, array $files)
    {
        $errors = [];
        /** @var StringField $name */ $name = $post['name'];
        if ($name->isEmpty()) $errors[] = static::E_NO_NAME;
        return $errors;
    }

    public function succeed(array $post, array $files)
    {
        $this->gender->name = $post['name']->get();
        $this->gender->update();
    }
}