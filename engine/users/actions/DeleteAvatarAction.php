<?php namespace engine\users\actions;

use frame\actions\ActionBody;
use frame\tools\Init;
use engine\users\User;
use frame\actions\fields\IntegerField;

/**
 * Параметры:
 * uid: id пользователя, аватар которого нужно удалить. Должен существовать.
 * Права: удаление аватара данного пользователя.
 */
class DeleteAvatarAction extends ActionBody
{
    /** @var User */
    private $user = null;

    public function listGet(): array
    {
        return [
            'uid' => IntegerField::class
        ];
    }

    public function initialize(array $get)
    {
        $this->user = User::selectIdentity($get['uid']->get());
        Init::require($this->user !== null);
        Init::accessOneRight('users', [
            'edit-all' => [$this->user], 
            'edit-own' => [$this->user]
        ]);
    }

    public function succeed(array $post, array $files)
    {
        if ($this->user->hasAvatar()) {
            unlink($this->user->getAvatarUrl());
            $this->user->avatar = null;
            $this->user->update();
        }
    }
}