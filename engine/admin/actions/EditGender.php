<?php namespace engine\admin\actions;

use frame\actions\ActionBody;
use engine\users\Gender;
use frame\tools\Init;
use engine\users\Group;

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
            'id' => [self::GET_INT, 'The id of the gender']
        ];
    }

    public function listPost(): array
    {
        return [
            'name' => [self::POST_TEXT, 'A new name of the gender']
        ];
    }

    public function initialize(array $get)
    {
        $this->gender = Gender::selectIdentity($get['id']);
        Init::require($this->gender !== null);
        Init::accessGroup(Group::ROOT_ID);
    }

    public function validate(array $post, array $files)
    {
        $errors = [];
        if (!$post['name']) $errors[] = static::E_NO_NAME;
        return $errors;
    }

    public function succeed(array $post, array $files)
    {
        $this->gender->name = $post['name'];
        $this->gender->update();
    }
}