<?php namespace frame\auth;

use frame\core\Driver;

/**
 * Driver for auth mechanism
 */
abstract class AuthDriver extends Driver
{
  public abstract function getUserMe(): GroupUser;
  public abstract function loadRights(int $moduleId, int $groupId): int;
  public abstract function saveRights(int $moduleId, int $groupId, int $rights);
}