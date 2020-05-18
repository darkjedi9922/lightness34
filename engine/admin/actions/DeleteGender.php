<?php namespace engine\admin\actions;

use engine\users\Gender;
use frame\actions\ActionBody;
use frame\auth\InitAccess;
use frame\actions\fields\IntegerField;
use frame\database\Records;
use frame\route\InitRoute;

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
        InitAccess::accessRight('users', 'configure-genders');
        $this->gender = Gender::selectIdentity($get['id']->get());
        InitRoute::require($this->gender !== null);
        InitRoute::require(!$this->gender->isDefault());
    }

    public function succeed(array $post, array $files)
    {
        Records::from('users', ['gender_id' => $this->gender->id])->update([
            'gender_id' => Gender::UNKNOWN_ID
        ]);
        $this->gender->delete();
    }
}