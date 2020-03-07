<?php namespace engine\admin\actions;

use engine\users\Gender;
use frame\actions\ActionBody;
use frame\tools\Init;
use engine\users\Group;
use frame\actions\fields\IntegerField;
use frame\database\Records;

/**
 * Параметры:
 * id: id пола. 
 * Должен существовать. 
 * Должен не быть стандартным.
 * Права: root.
 */
class DeleteGender extends ActionBody
{
    /** @var Gender */
    private $gender;

    public function listGet(): array
    {
        return [
            'id' => IntegerField::class
        ];
    }

    public function initialize(array $get)
    {
        Init::accessRight('users', 'configure-genders');
        $this->gender = Gender::selectIdentity($get['id']->get());
        Init::require($this->gender !== null);
        Init::require(!$this->gender->isDefault());
    }

    public function succeed(array $post, array $files)
    {
        Records::from('users', ['gender_id' => $this->gender->id])->update([
            'gender_id' => Gender::UNKNOWN_ID
        ]);
        $this->gender->delete();
    }
}