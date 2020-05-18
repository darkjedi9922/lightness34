<?php namespace engine\admin\actions;

use frame\actions\ActionBody;
use frame\auth\InitAccess;
use frame\route\InitRoute;
use engine\users\Group;
use frame\actions\fields\IntegerField;
use frame\actions\fields\StringField;

class EditUserGroupAction extends ActionBody
{
    /** @var Group */
    private $group;

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
        $this->group = Group::selectIdentity($get['id']->get());
        InitRoute::require($this->group !== null);
        InitAccess::accessGroup(Group::ROOT_ID);
    }

    public function succeed(array $post, array $files)
    {
        $this->group->name = $post['name']->get();
        $this->group->update();
    }
}
