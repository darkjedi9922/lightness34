<?php namespace frame\auth;

abstract class RightsStore extends \frame\core\Driver
{
    public abstract function loadRights(int $moduleId, int $groupId): int;
    public abstract function saveRights(int $moduleId, int $groupId, int $rights);
}