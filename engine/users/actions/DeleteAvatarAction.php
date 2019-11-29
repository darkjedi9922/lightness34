<?php namespace engine\users\actions;

use frame\actions\Action;
use frame\tools\Init;
use engine\users\User;

/**
 * Параметры:
 * uid: id пользователя, аватар которого нужно удалить. Должен существовать.
 * Права: удаление аватара данного пользователя.
 */
class DeleteAvatarAction extends Action
{
    /** @var User */
    private $user = null;

    protected function initialize()
    {
        $uid = $this->getData('get', 'uid');
        Init::require($uid !== null);
        $uid = (int) $uid;
        $this->user = User::selectIdentity($uid);
        Init::require($this->user !== null);
        Init::accessOneRight('users', [
            'edit-all' => $this->user, 
            'edit-own' => $this->user
        ]);
    }

    protected function succeed()
    {
        if ($this->user->hasAvatar()) {
            unlink($this->user->getAvatarUrl());
            $this->user->avatar = null;
            $this->user->update();
        }
    }
}