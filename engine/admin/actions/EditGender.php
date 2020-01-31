<?php namespace engine\admin\actions;

use frame\actions\ActionBody;
use engine\users\Gender;
use frame\tools\Init;
use engine\users\Group;
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
        $this->gender = Gender::selectIdentity($get['id']->get());
        Init::require($this->gender !== null);
        Init::accessGroup(Group::ROOT_ID);
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