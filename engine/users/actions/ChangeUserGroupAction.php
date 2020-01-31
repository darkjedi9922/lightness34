<?php namespace engine\users\actions;

use frame\actions\ActionBody;
use frame\tools\Init;
use engine\users\cash\user_me;
use engine\users\Group;
use engine\users\User;
use frame\actions\fields\IntegerField;

/**
 * Права: root.
 * Параметры: uid: id пользователя
 * Он должен существовать.
 * Он должен быть не root группой.
 * Данные: group_id: id новой группы.
 */
class ChangeUserGroupAction extends ActionBody
{
    /** @var User */
    private $user;

    public function listGet(): array
    {
        return [
            'uid' => IntegerField::class
        ];
    }

    public function listPost(): array
    {
        return [
            'group_id' => IntegerField::class
        ];
    }

    public function initialize(array $get)
    {
        $me = user_me::get();
        Init::access((int) $me->group_id === Group::ROOT_ID);
        $this->user = User::selectIdentity($get['uid']->get());
        Init::require($this->user !== null);
        Init::require($this->user->group_id !== Group::ROOT_ID);
    }

    public function validate(array $post, array $files): array
    {
        $id = $post['group_id']->get();
        if ($id !== $this->user->group_id) {
            $group = Group::selectIdentity($id);
            Init::require($group !== null);
            Init::require($group->id !== Group::GUEST_ID);
            Init::require($group->id !== Group::ROOT_ID);
        }

        return [];
    }

    public function succeed(array $post, array $files)
    {
        $id = $post['group_id']->get();
        if ($id !== $this->user->group_id) {
            $this->user->group_id = $id;
            $this->user->update();
        }
    }
}