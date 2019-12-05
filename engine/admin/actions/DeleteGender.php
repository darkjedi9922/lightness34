<?php namespace engine\admin\actions;

use engine\users\Gender;
use frame\actions\ActionBody;
use frame\tools\Init;
use engine\users\Group;
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
            'id' => [self::GET_INT, 'The id of the gender']
        ];
    }

    public function initialize(array $get)
    {
        $this->gender = Gender::selectIdentity($get['id']);
        Init::require($this->gender !== null);
        Init::require(!$this->gender->isDefault());
        Init::accessGroup(Group::ROOT_ID);
    }

    public function succeed(array $post, array $files)
    {
        Records::select('users', ['gender_id' => $this->gender->id])->update([
            'gender_id' => Gender::UNKNOWN_ID
        ]);
        $this->gender->delete();
    }
}