<?php namespace engine\users;

use frame\modules\Module;
use frame\auth\RightsDesc;
use frame\events\Events;
use engine\users\macros\UpdateUserStatus;
use engine\users\macros\UpdateOfflineUsers;
use frame\core\Core;

class UsersModule extends Module
{
    public function __construct(string $name, ?Module $parent = null)
    {
        parent::__construct($name, $parent);
        $events = Events::get();
        $events->on(Core::EVENT_APP_END, new UpdateUserStatus);
        $events->on(Core::EVENT_APP_END, new UpdateOfflineUsers);
    }
    
    public function createRightsDescription(): ?RightsDesc
    {
        return new UsersRightsDesc;
    }
}