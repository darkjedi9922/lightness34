<?php namespace engine\users\actions;

use frame\actions\Action;
use frame\tools\Init;
use engine\users\cash\user_me;
use engine\users\Group;
use engine\users\User;

/**
 * Права: root.
 * Параметры: uid: id пользователя
 * Он должен существовать.
 * Он должен быть не root группой.
 * Данные: group_id: id новой группы.
 */
class ChangeUserGroupAction extends Action
{
    /** @var User */
    private $user;

    protected function initialize()
    {
        $me = user_me::get();
        Init::access((int) $me->group_id === Group::ROOT_ID);
        $uid = (int) $this->getData('get', 'uid', -1);
        Init::require($uid !== -1);
        $this->user = User::selectIdentity($uid);
        Init::require($this->user !== null);
        Init::require($this->user->group_id !== Group::ROOT_ID);
    }

    protected function validate(): array
    {
        $id = (int) $this->getData('post', 'group_id', $this->user->group_id);
        if ($id !== $this->user->group_id) {
            $group = Group::selectIdentity($id);
            Init::require($group !== null);
            Init::require($group->id !== Group::GUEST_ID);
            Init::require($group->id !== Group::ROOT_ID);
        }

        return [];
    }

    protected function succeed()
    {
        $id = $this->getData('post', 'group_id', $this->user->group_id);
        if ($id !== $this->user->group_id) {
            $this->user->group_id = $id;
            $this->user->update();
        }
    }
}